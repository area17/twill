<?php

namespace A17\Twill\Repositories;

use A17\Twill\Facades\TwillCapsules;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;
use A17\Twill\Repositories\Behaviors\HandleBrowsers;
use A17\Twill\Repositories\Behaviors\HandleDates;
use A17\Twill\Repositories\Behaviors\HandleFieldsGroups;
use A17\Twill\Repositories\Behaviors\HandleRelatedBrowsers;
use A17\Twill\Repositories\Behaviors\HandleRepeaters;
use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PDO;
use ReflectionClass;

abstract class ModuleRepository
{
    use HandleDates, HandleBrowsers, HandleRelatedBrowsers, HandleRepeaters, HandleFieldsGroups;

    /**
     * @var \A17\Twill\Models\Model
     */
    protected $model;

    /**
     * @var string[]
     */
    protected $ignoreFieldsBeforeSave = [];

    /**
     * @var array
     */
    protected $countScope = [];

    /**
     * @var array
     */
    protected $fieldsGroups = [];

    /**
     * @var bool
     */
    public $fieldsGroupsFormFieldNamesAutoPrefix = false;

    /**
     * @var string|null
     */
    public $fieldsGroupsFormFieldNameSeparator = '_';

    /**
     * @param array $with
     * @param array $scopes
     * @param array $orders
     * @param int $perPage
     * @param bool $forcePagination
     * @return \Illuminate\Support\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function get($with = [], $scopes = [], $orders = [], $perPage = 20, $forcePagination = false)
    {
        $query = $this->model->with($with);

        $query = $this->filter($query, $scopes);
        $query = $this->order($query, $orders);

        if (!$forcePagination && $this->model instanceof Sortable) {
            return $query->ordered()->get();
        }

        if ($perPage == -1) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    /**
     * @param string $slug
     * @param array $scope
     * @return int
     */
    public function getCountByStatusSlug($slug, $scope = [])
    {
        $this->countScope = $scope;

        switch ($slug) {
            case 'all':
                return $this->getCountForAll();
            case 'published':
                return $this->getCountForPublished();
            case 'draft':
                return $this->getCountForDraft();
            case 'trash':
                return $this->getCountForTrash();
        }

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            if (($count = $this->$method($slug)) !== false) {
                return $count;
            }
        }

        return 0;
    }

    /**
     * @return int
     */
    public function getCountForAll()
    {
        $query = $this->model->newQuery();
        return $this->filter($query, $this->countScope)->count();
    }

    /**
     * @return int
     */
    public function getCountForPublished()
    {
        $query = $this->model->newQuery();
        return $this->filter($query, $this->countScope)->published()->count();
    }

    /**
     * @return int
     */
    public function getCountForDraft()
    {
        $query = $this->model->newQuery();
        return $this->filter($query, $this->countScope)->draft()->count();
    }

    /**
     * @return int
     */
    public function getCountForTrash()
    {
        $query = $this->model->newQuery();
        return $this->filter($query, $this->countScope)->onlyTrashed()->count();
    }

    /**
     * @param $id
     * @param array $with
     * @param array $withCount
     * @return \A17\Twill\Models\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById($id, $with = [], $withCount = [])
    {
        return $this->model->with($with)->withCount($withCount)->findOrFail($id);
    }

    /**
     * @param string $column
     * @param array $orders
     * @param null $exceptId
     * @return \Illuminate\Support\Collection
     */
    public function listAll($column = 'title', $orders = [], $exceptId = null)
    {
        $query = $this->model->newQuery();

        if ($exceptId) {
            $query = $query->where($this->model->getTable() . '.id', '<>', $exceptId);
        }

        if ($this->model instanceof Sortable) {
            $query = $query->ordered();
        } elseif (!empty($orders)) {
            $query = $this->order($query, $orders);
        }

        if ($this->model->isTranslatable()) {
            $query = $query->withTranslation();
        }

        return $query->get()->pluck($column, 'id');
    }

    /**
     * @param $search
     * @param array $fields
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function cmsSearch($search, $fields = [])
    {
        $query = $this->model->latest();

        $translatedAttributes = $this->model->translatedAttributes ?? [];

        foreach ($fields as $field) {
            if (in_array($field, $translatedAttributes)) {
                $query->orWhereHas('translations', function ($q) use ($field, $search) {
                    $q->where($field, $this->getLikeOperator(), "%{$search}%");
                });
            } else {
                $query->orWhere($field, $this->getLikeOperator(), "%{$search}%");
            }
        }

        return $query->get();
    }

    /**
     * @param $attributes
     * @param $fields
     * @return \A17\Twill\Models\Model
     */
    public function firstOrCreate($attributes, $fields = [])
    {
        return $this->model->where($attributes)->first() ?? $this->create($attributes + $fields);
    }

    /**
     * @param string[] $fields
     * @return \A17\Twill\Models\Model
     */
    public function create($fields)
    {
        return DB::transaction(function () use ($fields) {
            $original_fields = $fields;

            $fields = $this->prepareFieldsBeforeCreate($fields);

            $object = $this->model->create(Arr::except($fields, $this->getReservedFields()));

            $this->beforeSave($object, $original_fields);

            $fields = $this->prepareFieldsBeforeSave($object, $fields);

            $object->save();

            $this->afterSave($object, $fields);

            return $object;
        }, 3);
    }

    /**
     * @param array $fields
     * @return \A17\Twill\Models\Model
     */
    public function createForPreview($fields)
    {
        $fields = $this->prepareFieldsBeforeCreate($fields);

        $object = $this->model->newInstance(Arr::except($fields, $this->getReservedFields()));

        return $this->hydrate($object, $fields);
    }

    /**
     * @param array $attributes
     * @param array $fields
     * @return \A17\Twill\Models\Model|void
     */
    public function updateOrCreate($attributes, $fields)
    {
        $object = $this->model->where($attributes)->first();

        if (!$object) {
            return $this->create($fields);
        }

        $this->update($object->id, $fields);
    }

    /**
     * @param mixed $id
     * @param array $fields
     * @return void
     */
    public function update($id, $fields)
    {
        DB::transaction(function () use ($id, $fields) {
            $object = $this->model->findOrFail($id);

            $this->beforeSave($object, $fields);

            $fields = $this->prepareFieldsBeforeSave($object, $fields);

            $object->fill(Arr::except($fields, $this->getReservedFields()));

            $object->save();

            $this->afterSave($object, $fields);
        }, 3);
    }

    /**
     * @param mixed $id
     * @param array $values
     * @param array $scopes
     * @return mixed
     */
    public function updateBasic($id, $values, $scopes = [])
    {
        return DB::transaction(function () use ($id, $values, $scopes) {
            // apply scopes if no id provided
            if (is_null($id)) {
                $query = $this->model->query();

                foreach ($scopes as $column => $value) {
                    $query->where($column, $value);
                }

                $query->update($values);

                $query->get()->each(function ($object) use ($values) {
                    $this->afterUpdateBasic($object, $values);
                });

                return true;
            }

            // apply to all ids if array of ids provided
            if (is_array($id)) {
                $query = $this->model->whereIn('id', $id);
                $query->update($values);

                $query->get()->each(function ($object) use ($values) {
                    $this->afterUpdateBasic($object, $values);
                });

                return true;
            }

            if (($object = $this->model->find($id)) != null) {
                $object->update($values);
                $this->afterUpdateBasic($object, $values);
                return true;
            }

            return false;
        }, 3);
    }

    /**
     * @param array $ids
     * @return void
     */
    public function setNewOrder($ids)
    {
        DB::transaction(function () use ($ids) {
            $this->model->setNewOrder($ids);
        }, 3);
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function duplicate($id, $titleColumnKey = 'title')
    {

        if (($object = $this->model->find($id)) === null) {
            return false;
        }

        if (($revision = $object->revisions()->orderBy('created_at', 'desc')->first()) === null) {
            return false;
        }

        $revisionInput = json_decode($revision->payload, true);
        $baseInput = collect($revisionInput)->only([
            $titleColumnKey,
            'slug',
            'languages',
        ])->filter()->toArray();

        $newObject = $this->create($baseInput);

        $this->update($newObject->id, $revisionInput);

        return $newObject;
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            if (($object = $this->model->find($id)) === null) {
                return false;
            }

            if (!method_exists($object, 'canDeleteSafely') || $object->canDeleteSafely()) {
                $object->delete();
                $this->afterDelete($object);
                return true;
            }
            return false;
        }, 3);
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function bulkDelete($ids)
    {
        return DB::transaction(function () use ($ids) {
            try {
                Collection::make($ids)->each(function ($id) {
                    $this->delete($id);
                });
            } catch (Exception $e) {
                Log::error($e);
                if (config('app.debug')) {
                    throw $e;
                }
                return false;
            }

            return true;
        }, 3);
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function forceDelete($id)
    {
        return DB::transaction(function () use ($id) {
            if (($object = $this->model->onlyTrashed()->find($id)) === null) {
                return false;
            } else {
                $object->forceDelete();
                $this->afterDelete($object);
                return true;
            }
        }, 3);
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function bulkForceDelete($ids)
    {
        return DB::transaction(function () use ($ids) {
            try {
                $query = $this->model->onlyTrashed()->whereIn('id', $ids);
                $objects = $query->get();

                $query->forceDelete();

                $objects->each(function ($object) {
                    $this->afterDelete($object);
                });
            } catch (Exception $e) {
                Log::error($e);
                return false;
            }

            return true;
        }, 3);
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function restore($id)
    {
        return DB::transaction(function () use ($id) {
            if (($object = $this->model->withTrashed()->find($id)) != null) {
                $object->restore();
                $this->afterRestore($object);
                return true;
            }

            return false;
        }, 3);
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function bulkRestore($ids)
    {
        return DB::transaction(function () use ($ids) {
            try {
                $query = $this->model->withTrashed()->whereIn('id', $ids);
                $objects = $query->get();

                $query->restore();

                $objects->each(function ($object) {
                    $this->afterRestore($object);
                });
            } catch (Exception $e) {
                Log::error($e);
                return false;
            }

            return true;
        }, 3);
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return array
     */
    public function cleanupFields($object, $fields)
    {
        if (property_exists($this->model, 'checkboxes')) {
            foreach ($this->model->checkboxes as $field) {
                if (!$this->shouldIgnoreFieldBeforeSave($field)) {
                    if (!isset($fields[$field])) {
                        $fields[$field] = false;
                    } else {
                        $fields[$field] = !empty($fields[$field]);
                    }
                }
            }
        }

        if (property_exists($this->model, 'nullable')) {
            foreach ($this->model->nullable as $field) {
                if (!isset($fields[$field]) && !$this->shouldIgnoreFieldBeforeSave($field)) {
                    $fields[$field] = null;
                }
            }
        }

        foreach ($fields as $key => $value) {
            if (!$this->shouldIgnoreFieldBeforeSave($key)) {
                if (is_array($value) && empty($value)) {
                    $fields[$key] = null;
                }
                if ($value === '') {
                    $fields[$key] = null;
                }
            }
        }

        return $fields;
    }

    /**
     * @param array $fields
     * @return array
     */
    public function prepareFieldsBeforeCreate($fields)
    {
        $fields = $this->cleanupFields(null, $fields);

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $fields = $this->$method($fields);
        }

        return $fields;
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return string[]
     */
    public function prepareFieldsBeforeSave($object, $fields)
    {
        $fields = $this->cleanupFields($object, $fields);

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $fields = $this->$method($object, $fields);
        }

        return $fields;
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return void
     */
    public function afterUpdateBasic($object, $fields)
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object, $fields);
        }
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return void
     */
    public function beforeSave($object, $fields)
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object, $fields);
        }
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return void
     */
    public function afterSave($object, $fields)
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object, $fields);
        }
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @return void
     */
    public function afterDelete($object)
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object);
        }
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @return void
     */
    public function afterRestore($object)
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object);
        }
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return \A17\Twill\Models\Model
     */
    public function hydrate($object, $fields)
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $object = $this->$method($object, $fields);
        }

        return $object;
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @return array
     */
    public function getFormFields($object)
    {
        $fields = $object->attributesToArray();

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $fields = $this->$method($object, $fields);
        }

        return $fields;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $scopes
     * @return \Illuminate\Database\Query\Builder
     */
    public function filter($query, array $scopes = [])
    {
        $likeOperator = $this->getLikeOperator();

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($query, $scopes);
        }

        unset($scopes['search']);

        if (isset($scopes['exceptIds'])) {
            $query->whereNotIn($this->model->getTable() . '.id', $scopes['exceptIds']);
            unset($scopes['exceptIds']);
        }

        foreach ($scopes as $column => $value) {
            if (method_exists($this->model, 'scope' . ucfirst($column))) {
                $query->$column();
            } else {
                if (is_array($value)) {
                    $query->whereIn($column, $value);
                } elseif ($column[0] == '%') {
                    $value && ($value[0] == '!') ? $query->where(substr($column, 1), "not $likeOperator", '%' . substr($value, 1) . '%') : $query->where(substr($column, 1), $likeOperator, '%' . $value . '%');
                } elseif (isset($value[0]) && $value[0] == '!') {
                    $query->where($column, '<>', substr($value, 1));
                } elseif ($value !== '') {
                    $query->where($column, $value);
                }
            }
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $orders
     * @return \Illuminate\Database\Query\Builder
     */
    public function order($query, array $orders = [])
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($query, $orders);
        }

        foreach ($orders as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query;
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @param string $relationship
     * @param string $formField
     * @param string $attribute
     * @return void
     */
    public function updateOneToMany($object, $fields, $relationship, $formField, $attribute)
    {
        if (isset($fields[$formField])) {
            foreach ($fields[$formField] as $id) {
                $object->$relationship()->updateOrCreate([$attribute => $id]);
            }

            foreach ($object->$relationship as $relationshipObject) {
                if (!in_array($relationshipObject->$attribute, $fields[$formField])) {
                    $relationshipObject->delete();
                }
            }
        } else {
            $object->$relationship()->delete();
        }
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @param string $relationship
     * @return void
     */
    public function updateMultiSelect($object, $fields, $relationship)
    {
        $object->$relationship()->sync($fields[$relationship] ?? []);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $scopes
     * @param string $scopeField
     * @param string $scopeRelation
     * @return void
     */
    public function addRelationFilterScope($query, &$scopes, $scopeField, $scopeRelation)
    {
        if (isset($scopes[$scopeField])) {
            $id = $scopes[$scopeField];
            $query->whereHas($scopeRelation, function ($query) use ($id, $scopeField) {
                $query->where($scopeField, $id);
            });
            unset($scopes[$scopeField]);
        }
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $scopes
     * @param string $scopeField
     * @return void
     */
    public function addLikeFilterScope($query, &$scopes, $scopeField)
    {
        if (isset($scopes[$scopeField]) && is_string($scopes[$scopeField])) {
            $query->where($scopeField, $this->getLikeOperator(), '%' . $scopes[$scopeField] . '%');
            unset($scopes[$scopeField]);
        }
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $scopes
     * @param string $scopeField
     * @param string[] $orFields
     */
    public function searchIn($query, &$scopes, $scopeField, $orFields = [])
    {

        if (isset($scopes[$scopeField]) && is_string($scopes[$scopeField])) {
            $query->where(function ($query) use (&$scopes, $scopeField, $orFields) {
                foreach ($orFields as $field) {
                    $query->orWhere($field, $this->getLikeOperator(), '%' . $scopes[$scopeField] . '%');
                    unset($scopes[$field]);
                }
            });
        }
    }

    /**
     * @return bool
     */
    public function isUniqueFeature()
    {
        return false;
    }

    /**
     * @param array $ignore
     * @return void
     */
    public function addIgnoreFieldsBeforeSave($ignore = [])
    {
        $this->ignoreFieldsBeforeSave = is_array($ignore)
        ? array_merge($this->ignoreFieldsBeforeSave, $ignore)
        : array_merge($this->ignoreFieldsBeforeSave, [$ignore]);
    }

    /**
     * @param string $ignore
     * @return bool
     */
    public function shouldIgnoreFieldBeforeSave($ignore)
    {
        return in_array($ignore, $this->ignoreFieldsBeforeSave);
    }

    /**
     * @return string[]
     */
    public function getReservedFields()
    {
        return [
            'medias',
            'browsers',
            'repeaters',
            'blocks',
        ];
    }

    /**
     * @param string $relation
     * @param \A17\Twill\Models\Model|\A17\Twill\Repositories\ModuleRepository|null $modelOrRepository
     * @return mixed
     */
    protected function getModelRepository($relation, $modelOrRepository = null)
    {
        if (!$modelOrRepository) {
            if (class_exists($relation) && (new $relation) instanceof Model) {
                $modelOrRepository = Str::afterLast($relation, '\\');
            } else {
                $morphedModel = Relation::getMorphedModel($relation);
                if (class_exists($morphedModel) && (new $morphedModel) instanceof Model) {
                    $modelOrRepository = (new ReflectionClass($morphedModel))->getShortName();
                } else {
                    $modelOrRepository = ucfirst(Str::singular($relation));
                }
            }
        }

        $repository = class_exists($modelOrRepository)
        ? App::make($modelOrRepository)
        : $modelOrRepository;

        if ($repository instanceof ModuleRepository) {
            return $repository;
        }

        $class = Config::get('twill.namespace') . "\\Repositories\\" . ucfirst($modelOrRepository) . "Repository";

        if (class_exists($class)) {
            return App::make($class);
        }

        $capsule = TwillCapsules::getCapsuleForModel($modelOrRepository);

        if (blank($capsule)) {
            throw new Exception("Repository class not found for model '{$modelOrRepository}'");
        }

        return App::make($capsule->getRepositoryClass());
    }

    /**
     * @param string|null $method
     * @return array
     */
    protected function traitsMethods(string $method = null)
    {
        $method = $method ?? debug_backtrace()[1]['function'];

        $traits = array_values(class_uses_recursive(get_called_class()));

        $uniqueTraits = array_unique(array_map('class_basename', $traits));

        $methods = array_map(function (string $trait) use ($method) {
            return $method . $trait;
        }, $uniqueTraits);

        return array_filter($methods, function (string $method) {
            return method_exists(get_called_class(), $method);
        });
    }

    /**
     * @return string
     */
    protected function getLikeOperator()
    {
        if (DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql') {
            return 'ILIKE';
        }

        return 'LIKE';
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->model->$method(...$parameters);
    }

    /**
     * @param string $behavior
     * @return boolean
     */
    public function hasBehavior($behavior)
    {
        $hasBehavior = classHasTrait($this, 'A17\Twill\Repositories\Behaviors\Handle' . ucfirst($behavior));

        if (Str::startsWith($behavior, 'translation')) {
            $hasBehavior = $hasBehavior && $this->model->isTranslatable();
        }

        return $hasBehavior;
    }

    /**
     * @return boolean
     */
    public function isTranslatable($column)
    {
        return $this->model->isTranslatable($column);
    }
}
