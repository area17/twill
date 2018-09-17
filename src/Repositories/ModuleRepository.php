<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Repositories\Behaviors\HandleDates;
use DB;
use Log;
use PDO;

abstract class ModuleRepository
{
    use HandleDates;

    protected $model;

    protected $ignoreFieldsBeforeSave = [];

    protected $countScope = [];

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

        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'getCountByStatusSlug' . class_basename($trait))) {
                if ($count = $this->$method($slug)) {
                    return $count;
                }
            }
        }

        return 0;
    }

    public function getCountForAll()
    {
        return $this->model->where($this->countScope)->count();
    }

    public function getCountForPublished()
    {
        return $this->model->where($this->countScope)->published()->count();
    }

    public function getCountForDraft()
    {
        return $this->model->where($this->countScope)->draft()->count();
    }

    public function getCountForTrash()
    {
        return $this->model->where($this->countScope)->onlyTrashed()->count();
    }

    public function getById($id, $with = [], $withCount = [])
    {
        return $this->model->with($with)->withCount($withCount)->findOrFail($id);
    }

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

        if (property_exists($this->model, 'translatedAttributes')) {
            $query = $query->withTranslation();
        }

        return $query->get()->pluck($column, 'id');
    }

    public function cmsSearch($search, $fields = [])
    {
        $query = $this->model->latest();

        $translatedAttributes = $this->model->translatedAttributes ?? [];

        foreach ($fields as $field) {
            if (in_array($field, $translatedAttributes)) {
                $query->orWhereHas('translations', function ($q) use ($field, $search) {
                    $q->orWhere($field, $this->getLikeOperator(), "%{$search}%");
                });
            } else {
                $query->orWhere($field, $this->getLikeOperator(), "%{$search}%");
            }
        }

        return $query->get();
    }

    public function firstOrCreate($attributes, $fields)
    {
        return $this->model->where($attributes)->first() ?? $this->create($fields);
    }

    public function create($fields)
    {
        return DB::transaction(function () use ($fields) {
            $original_fields = $fields;

            $fields = $this->prepareFieldsBeforeCreate($fields);

            $object = $this->model->create(array_except($fields, $this->getReservedFields()));

            $this->beforeSave($object, $original_fields);

            $fields = $this->prepareFieldsBeforeSave($object, $fields);

            $object->push();

            $this->afterSave($object, $fields);

            return $object;
        }, 3);
    }

    public function createForPreview($fields)
    {
        $fields = $this->prepareFieldsBeforeCreate($fields);

        $object = $this->model->newInstance(array_except($fields, $this->getReservedFields()));

        return $this->hydrate($object, $fields);
    }

    public function update($id, $fields)
    {
        DB::transaction(function () use ($id, $fields) {
            $object = $this->model->findOrFail($id);

            $this->beforeSave($object, $fields);

            $fields = $this->prepareFieldsBeforeSave($object, $fields);

            $object->fill(array_except($fields, $this->getReservedFields()));

            $object->push();

            $this->afterSave($object, $fields);
        }, 3);
    }

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

    public function setNewOrder($ids)
    {
        DB::transaction(function () use ($ids) {
            $this->model->setNewOrder($ids);
        }, 3);
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            if (($object = $this->model->find($id)) != null) {
                $object->delete();
                $this->afterDelete($object);
                return true;
            }

            return false;
        }, 3);
    }

    public function bulkDelete($ids)
    {
        return DB::transaction(function () use ($ids) {
            try {
                $query = $this->model->whereIn('id', $ids);
                $objects = $query->get();

                $query->delete();

                $objects->each(function ($object) {
                    $this->afterDelete($object);
                });
            } catch (\Exception $e) {
                Log::error($e);
                return false;
            }

            return true;
        }, 3);
    }

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
            } catch (\Exception $e) {
                Log::error($e);
                return false;
            }

            return true;
        }, 3);
    }

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

    public function prepareFieldsBeforeCreate($fields)
    {
        $fields = $this->cleanupFields(null, $fields);

        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'prepareFieldsBeforeCreate' . class_basename($trait))) {
                $fields = $this->$method($fields);
            }
        }

        return $fields;
    }

    public function prepareFieldsBeforeSave($object, $fields)
    {
        $fields = $this->cleanupFields($object, $fields);

        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'prepareFieldsBeforeSave' . class_basename($trait))) {
                $fields = $this->$method($object, $fields);
            }
        }

        return $fields;
    }

    public function afterUpdateBasic($object, $fields)
    {
        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'afterUpdateBasic' . class_basename($trait))) {
                $this->$method($object, $fields);
            }
        }
    }

    public function beforeSave($object, $fields)
    {
        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'beforeSave' . class_basename($trait))) {
                $this->$method($object, $fields);
            }
        }
    }

    public function afterSave($object, $fields)
    {
        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'afterSave' . class_basename($trait))) {
                $this->$method($object, $fields);
            }
        }
    }

    public function afterDelete($object)
    {
        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'afterDelete' . class_basename($trait))) {
                $this->$method($object);
            }
        }
    }

    public function afterRestore($object)
    {
        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'afterRestore' . class_basename($trait))) {
                $this->$method($object);
            }
        }
    }

    public function hydrate($object, $fields)
    {
        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'hydrate' . class_basename($trait))) {
                $object = $this->$method($object, $fields);
            }
        }

        return $object;
    }

    public function getFormFields($object)
    {
        $fields = $object->attributesToArray();

        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'getFormFields' . class_basename($trait))) {
                $fields = $this->$method($object, $fields);
            }
        }

        return $fields;
    }

    public function filter($query, array $scopes = [])
    {
        $likeOperator = $this->getLikeOperator();

        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'filter' . class_basename($trait))) {
                $this->$method($query, $scopes);
            }
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

    public function order($query, array $orders = [])
    {
        foreach ($orders as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'order' . class_basename($trait))) {
                $this->$method($query, $orders);
            }
        }

        return $query;
    }

    public function getFormFieldsForBrowser($object, $relation, $routePrefix = null, $titleKey = 'title', $moduleName = null)
    {
        return $object->$relation->map(function ($relatedElement) use ($titleKey, $routePrefix, $relation, $moduleName) {
            return [
                'id' => $relatedElement->id,
                'name' => $relatedElement->titleInBrowser ?? $relatedElement->$titleKey,
                'edit' => moduleRoute($moduleName ?? $relation, $routePrefix ?? '', 'edit', $relatedElement->id),
            ] + (classHasTrait($relatedElement, HasMedias::class) ? [
                'thumbnail' => $relatedElement->defaultCmsImage(['w' => 100, 'h' => 100]),
            ] : []);
        })->toArray();
    }

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

    public function updateOrderedBelongsTomany($object, $fields, $relationship, $positionAttribute = 'position')
    {
        $fieldsHasElements = isset($fields['browsers'][$relationship]) && !empty($fields['browsers'][$relationship]);
        $relatedElements = $fieldsHasElements ? $fields['browsers'][$relationship] : [];
        $relatedElementsWithPosition = [];
        $position = 1;
        foreach ($relatedElements as $relatedElement) {
            $relatedElementsWithPosition[$relatedElement['id']] = [$positionAttribute => $position++];
        }

        $object->$relationship()->sync($relatedElementsWithPosition);
    }

    public function updateBrowser($object, $fields, $relationship, $positionAttribute = 'position')
    {
        $this->updateOrderedBelongsTomany($object, $fields, $relationship, $positionAttribute);
    }

    public function updateMultiSelect($object, $fields, $relationship)
    {
        $object->$relationship()->sync($fields[$relationship] ?? []);
    }

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

    public function addLikeFilterScope($query, &$scopes, $scopeField)
    {
        if (isset($scopes[$scopeField]) && is_string($scopes[$scopeField])) {
            $query->where($scopeField, $this->getLikeOperator(), '%' . $scopes[$scopeField] . '%');
            unset($scopes[$scopeField]);
        }
    }

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

    public function isUniqueFeature()
    {
        return false;
    }

    public function addIgnoreFieldsBeforeSave($ignore = [])
    {
        $this->ignoreFieldsBeforeSave = is_array($ignore)
        ? array_merge($this->ignoreFieldsBeforeSave, $ignore)
        : array_merge($this->ignoreFieldsBeforeSave, [$ignore]);
    }

    public function shouldIgnoreFieldBeforeSave($ignore)
    {
        return in_array($ignore, $this->ignoreFieldsBeforeSave);
    }

    public function getReservedFields()
    {
        return [
            'medias',
            'browsers',
            'repeaters',
            'blocks',
        ];
    }

    protected function getModelRepository($relation, $model = null)
    {
        if (!$model) {
            $model = ucfirst(str_singular($relation));
        }

        return app(config('twill.namespace') . "\\Repositories\\" . ucfirst($model) . "Repository");
    }

    private function getLikeOperator()
    {
        if (DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql') {
            return 'ILIKE';
        }

        return 'LIKE';
    }

    public function __call($method, $parameters)
    {
        return $this->model->$method(...$parameters);
    }
}
