<?php

namespace A17\Twill\Repositories;

use A17\Twill\Facades\TwillCapsules;
use A17\Twill\Exceptions\NoCapsuleFoundException;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;
use A17\Twill\Repositories\Behaviors\HandleBrowsers;
use A17\Twill\Repositories\Behaviors\HandleDates;
use A17\Twill\Repositories\Behaviors\HandleFieldsGroups;
use A17\Twill\Repositories\Behaviors\HandlePermissions;
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
    use HandleDates;
    use HandleBrowsers;
    use HandleRelatedBrowsers;
    use HandleRepeaters;
    use HandleFieldsGroups;
    use HandlePermissions;

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
     * @return \Illuminate\Support\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @param mixed[] $with
     * @param mixed[] $scopes
     * @param mixed[] $orders
     */
    public function get(array $with = [], array $scopes = [], array $orders = [], int $perPage = 20, bool $forcePagination = false)
    {
        $query = $this->model->with($with);

        $query = $this->filter($query, $scopes);
        $query = $this->order($query, $orders);

        if (! $forcePagination && $this->model instanceof Sortable) {
            return $query->ordered()->get();
        }

        if ($perPage == -1) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    /**
     * @return int
     * @param mixed[] $scope
     */
    public function getCountByStatusSlug(string $slug, array $scope = [])
    {
        $query = $this->model->where($scope);

        if (config('twill.enabled.permissions-management') &&
            (isPermissionableModule(getModuleNameByModel($this->model)) || method_exists($this->model, 'scopeAccessible'))
        ) {
            $query = $query->accessible();
        }

        switch ($slug) {
            case 'all':
                return $query->count();
            case 'published':
                return $query->published()->count();
            case 'draft':
                return $query->draft()->count();
            case 'trash':
                return $query->onlyTrashed()->count();
        }

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            if (($count = $this->$method($slug, $scope)) !== false) {
                return $count;
            }
        }

        return 0;
    }

    /**
     * @deprecated To be removed in Twill 3.0
     * @return int
     */
    public function getCountForAll()
    {
        $query = $this->model->newQuery();

        return $this->filter($query, $this->countScope)->count();
    }

    /**
     * @deprecated To be removed in Twill 3.0
     * @return int
     */
    public function getCountForPublished()
    {
        $query = $this->model->newQuery();

        return $this->filter($query, $this->countScope)->published()->count();
    }

    /**
     * @deprecated To be removed in Twill 3.0
     * @return int
     */
    public function getCountForDraft()
    {
        $query = $this->model->newQuery();

        return $this->filter($query, $this->countScope)->draft()->count();
    }

    /**
     * @deprecated To be removed in Twill 3.0
     * @return int
     */
    public function getCountForTrash()
    {
        $query = $this->model->newQuery();

        return $this->filter($query, $this->countScope)->onlyTrashed()->count();
    }

    /**
     * @param $id
     * @return \A17\Twill\Models\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById($id, array $with = [], array $withCount = [])
    {
        return $this->model->with($with)->withCount($withCount)->findOrFail($id);
    }

    /**
     * @param null $exceptId
     * @return \Illuminate\Support\Collection
     * @param mixed[] $orders
     */
    public function listAll(string $column = 'title', array $orders = [], $exceptId = null)
    {
        $query = $this->model->newQuery();

        if ($exceptId) {
            $query = $query->where($this->model->getTable() . '.id', '<>', $exceptId);
        }

        if ($this->model instanceof Sortable) {
            $query = $query->ordered();
        } elseif (! empty($orders)) {
            $query = $this->order($query, $orders);
        }

        if ($this->model->isTranslatable()) {
            $query = $query->withTranslation();
        }

        return $query->get()->pluck($column, 'id');
    }

    /**
     * @param $search
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function cmsSearch($search, array $fields = [])
    {
        $query = $this->model->latest();

        $translatedAttributes = $this->model->getTranslatedAttributes() ?? [];

        foreach ($fields as $field) {
            if (in_array($field, $translatedAttributes)) {
                $query->orWhereHas('translations', function ($q) use ($field, $search) {
                    $q->where($field, $this->getLikeOperator(), sprintf('%%%s%%', $search));
                });
            } else {
                $query->orWhere($field, $this->getLikeOperator(), sprintf('%%%s%%', $search));
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
    public function create(array $fields)
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
     * @return \A17\Twill\Models\Model
     * @param mixed[] $fields
     */
    public function createForPreview(array $fields)
    {
        $fields = $this->prepareFieldsBeforeCreate($fields);

        $object = $this->model->newInstance(Arr::except($fields, $this->getReservedFields()));

        return $this->hydrate($object, $fields);
    }

    public function updateOrCreate(array $attributes, array $fields): Model
    {
        $object = $this->model->where($attributes)->first();

        if (! $object) {
            return $this->create($fields);
        }

        $this->update($object->id, $fields);

        return $object;
    }

    /**
     * @param mixed $id
     * @return void
     * @param mixed[] $fields
     */
    public function update($id, array $fields)
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
     * @return mixed
     * @param mixed[] $values
     * @param mixed[] $scopes
     */
    public function updateBasic($id, array $values, array $scopes = [])
    {
        return DB::transaction(function () use ($id, $values, $scopes): bool {
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
     * @return void
     * @param mixed[] $ids
     */
    public function setNewOrder(array $ids)
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
        return DB::transaction(function () use ($id): bool {
            if (($object = $this->model->find($id)) === null) {
                return false;
            }

            if (! method_exists($object, 'canDeleteSafely') || $object->canDeleteSafely()) {
                $object->delete();
                $this->afterDelete($object);

                return true;
            }

            return false;
        }, 3);
    }

    /**
     * @return mixed
     * @param mixed[] $ids
     */
    public function bulkDelete(array $ids)
    {
        return DB::transaction(function () use ($ids): bool {
            try {
                Collection::make($ids)->each(function ($id) {
                    $this->delete($id);
                });
            } catch (Exception $exception) {
                Log::error($exception);
                if (config('app.debug')) {
                    throw $exception;
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
        return DB::transaction(function () use ($ids): bool {
            try {
                $query = $this->model->onlyTrashed()->whereIn('id', $ids);
                $objects = $query->get();

                $query->forceDelete();

                $objects->each(function ($object) {
                    $this->afterDelete($object);
                });
            } catch (Exception $exception) {
                Log::error($exception);

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
        return DB::transaction(function () use ($id): bool {
            if (($object = $this->model->withTrashed()->find($id)) != null) {
                $object->restore();
                $this->afterRestore($object);

                return true;
            }

            return false;
        }, 3);
    }

    /**
     * @return mixed
     * @param mixed[] $ids
     */
    public function bulkRestore(array $ids)
    {
        return DB::transaction(function () use ($ids): bool {
            try {
                $query = $this->model->withTrashed()->whereIn('id', $ids);
                $objects = $query->get();

                $query->restore();

                $objects->each(function ($object) {
                    $this->afterRestore($object);
                });
            } catch (Exception $exception) {
                Log::error($exception);

                return false;
            }

            return true;
        }, 3);
    }

    /**
     * @return array
     * @param mixed[] $fields
     */
    public function cleanupFields(\A17\Twill\Models\Model $object, array $fields)
    {
        if (property_exists($this->model, 'checkboxes')) {
            foreach ($this->model->checkboxes as $field) {
                if (! $this->shouldIgnoreFieldBeforeSave($field)) {
                    $fields[$field] = isset($fields[$field]) && ! empty($fields[$field]);
                }
            }
        }

        if (property_exists($this->model, 'nullable')) {
            foreach ($this->model->nullable as $field) {
                if (! isset($fields[$field]) && ! $this->shouldIgnoreFieldBeforeSave($field)) {
                    $fields[$field] = null;
                }
            }
        }

        foreach ($fields as $key => $value) {
            if (! $this->shouldIgnoreFieldBeforeSave($key)) {
                if ($value === []) {
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
     * @return array
     * @param mixed[] $fields
     */
    public function prepareFieldsBeforeCreate(array $fields)
    {
        $fields = $this->cleanupFields(null, $fields);

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $fields = $this->$method($fields);
        }

        return $fields;
    }

    /**
     * @return string[]
     * @param mixed[] $fields
     */
    public function prepareFieldsBeforeSave(\A17\Twill\Models\Model $object, array $fields)
    {
        $fields = $this->cleanupFields($object, $fields);

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $fields = $this->$method($object, $fields);
        }

        return $fields;
    }

    /**
     * @return void
     * @param mixed[] $fields
     */
    public function afterUpdateBasic(\A17\Twill\Models\Model $object, array $fields)
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object, $fields);
        }
    }

    /**
     * @return void
     * @param mixed[] $fields
     */
    public function beforeSave(\A17\Twill\Models\Model $object, array $fields)
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object, $fields);
        }
    }

    /**
     * @return void
     * @param mixed[] $fields
     */
    public function afterSave(\A17\Twill\Models\Model $object, array $fields)
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object, $fields);
        }
    }

    /**
     * @return void
     */
    public function afterDelete(\A17\Twill\Models\Model $object)
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object);
        }
    }

    /**
     * @return void
     */
    public function afterRestore(\A17\Twill\Models\Model $object)
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object);
        }
    }

    /**
     * @return \A17\Twill\Models\Model
     * @param mixed[] $fields
     */
    public function hydrate(\A17\Twill\Models\Model $object, array $fields)
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $object = $this->$method($object, $fields);
        }

        return $object;
    }

    /**
     * @return array
     */
    public function getFormFields(\A17\Twill\Models\Model $object)
    {
        $fields = $object->attributesToArray();

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $fields = $this->$method($object, $fields);
        }

        return $fields;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter(\Illuminate\Database\Eloquent\Builder $query, array $scopes = [])
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
            } elseif (is_array($value)) {
                $query->whereIn($column, $value);
            } elseif ($column[0] == '%') {
                $value && ($value[0] == '!') ? $query->where(substr($column, 1), sprintf('not %s', $likeOperator), '%' . substr($value, 1) . '%') : $query->where(substr($column, 1), $likeOperator, '%' . $value . '%');
            } elseif (isset($value[0]) && $value[0] == '!') {
                $query->where($column, '<>', substr($value, 1));
            } elseif ($value !== '') {
                $query->where($column, $value);
            }
        }

        return $query;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function order(\Illuminate\Database\Eloquent\Builder $query, array $orders = [])
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
     * @return void
     * @param mixed[] $fields
     */
    public function updateOneToMany(\A17\Twill\Models\Model $object, array $fields, string $relationship, string $formField, string $attribute)
    {
        if (isset($fields[$formField])) {
            foreach ($fields[$formField] as $id) {
                $object->$relationship()->updateOrCreate([$attribute => $id]);
            }

            foreach ($object->$relationship as $relationshipObject) {
                if (! in_array($relationshipObject->$attribute, $fields[$formField])) {
                    $relationshipObject->delete();
                }
            }
        } else {
            $object->$relationship()->delete();
        }
    }

    /**
     * @return void
     * @param mixed[] $fields
     */
    public function updateMultiSelect(\A17\Twill\Models\Model $object, array $fields, string $relationship)
    {
        $object->$relationship()->sync($fields[$relationship] ?? []);
    }

    /**
     * @return void
     * @param mixed[] $scopes
     */
    public function addRelationFilterScope(\Illuminate\Database\Eloquent\Builder $query, array &$scopes, string $scopeField, string $scopeRelation)
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
     * @return void
     * @param mixed[] $scopes
     */
    public function addLikeFilterScope(\Illuminate\Database\Eloquent\Builder $query, array &$scopes, string $scopeField)
    {
        if (isset($scopes[$scopeField]) && is_string($scopes[$scopeField])) {
            $query->where($scopeField, $this->getLikeOperator(), '%' . $scopes[$scopeField] . '%');
            unset($scopes[$scopeField]);
        }
    }

    /**
     * @param string[] $orFields
     * @param mixed[] $scopes
     */
    public function searchIn(\Illuminate\Database\Eloquent\Builder $query, array &$scopes, string $scopeField, array $orFields = [])
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
     * @return void
     * @param mixed[] $ignore
     */
    public function addIgnoreFieldsBeforeSave(array $ignore = [])
    {
        $this->ignoreFieldsBeforeSave = is_array($ignore)
        ? array_merge($this->ignoreFieldsBeforeSave, $ignore)
        : array_merge($this->ignoreFieldsBeforeSave, [$ignore]);
    }

    /**
     * @return bool
     */
    public function shouldIgnoreFieldBeforeSave(string $ignore)
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
     * @param \A17\Twill\Models\Model|\A17\Twill\Repositories\ModuleRepository|null $modelOrRepository
     * @return mixed
     */
    protected function getModelRepository(string $relation, $modelOrRepository = null)
    {
        if ($modelOrRepository === null) {
            if (class_exists($relation) && (new $relation()) instanceof Model) {
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

        $class = Config::get('twill.namespace') . '\\Repositories\\' . ucfirst($modelOrRepository) . 'Repository';

        if (class_exists($class)) {
            return App::make($class);
        }

        try {
            $capsule = TwillCapsules::getCapsuleForModel($modelOrRepository);

            return App::make($capsule->getRepositoryClass());
        } catch (NoCapsuleFoundException) {
            throw new \Exception(sprintf('Repository class not found for model \'%s\'', $modelOrRepository));
        }
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

        $methods = array_map(function (string $trait) use ($method): string {
            return $method . $trait;
        }, $uniqueTraits);

        return array_filter($methods, function (string $method): bool {
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
     * @return bool
     */
    public function hasBehavior(string $behavior)
    {
        $hasBehavior = classHasTrait($this, 'A17\Twill\Repositories\Behaviors\Handle' . ucfirst($behavior));

        if (Str::startsWith($behavior, 'translation')) {
            $hasBehavior = $hasBehavior && $this->model->isTranslatable();
        }

        return $hasBehavior;
    }

    /**
     * @return bool
     */
    public function isTranslatable($column)
    {
        return $this->model->isTranslatable($column);
    }
}
