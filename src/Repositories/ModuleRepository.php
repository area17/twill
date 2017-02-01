<?php

namespace A17\CmsToolkit\Repositories;

use A17\CmsToolkit\Models\Behaviors\Sortable;
use A17\CmsToolkit\Repositories\Behaviors\HandleDates;

abstract class ModuleRepository
{
    use HandleDates;

    protected $model;

    public function get($with = [], $scopes = [], $orders = [], $perPage = 15)
    {
        $query = $this->model->with($with);

        $query = $this->filter($query, $scopes);
        $query = $this->order($query, $orders);

        if ($this->model instanceof Sortable) {
            return $query->ordered()->get();
        }

        if ($perPage == -1) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    public function getById($id, $with = [])
    {
        return $this->model->with($with)->findOrFail($id);
    }

    public function listAll($column = 'name', $orders = [], $exceptId = null)
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

    public function create($fields)
    {
        $fields = $this->prepareFieldsBeforeCreate($fields);

        $object = $this->model->create($fields);

        $fields = $this->prepareFieldsBeforeSave($object, $fields);

        $object->push();

        $this->afterSave($object, $fields);

        return $object;
    }

    public function update($id, $fields)
    {
        $object = $this->model->findOrFail($id);

        $fields = $this->prepareFieldsBeforeSave($object, $fields);

        $object->fill($fields);

        $object->push();

        $this->afterSave($object, $fields);
    }

    public function updateBasic($id, $values)
    {
        if (is_null($id)) {
            $this->model->query()->update($values);
        }

        if (($object = $this->model->find($id)) != null) {
            $object->update($values);

            $this->afterUpdateBasic($object, $values);
        }
    }

    public function setNewOrder($ids)
    {
        $this->model->setNewOrder($ids);
    }

    public function delete($id)
    {
        if (($object = $this->model->find($id)) != null) {
            $object->delete();
        }
    }

    public function cleanupFields($object, $fields)
    {
        if (property_exists($this->model, 'checkboxes')) {
            foreach ($this->model->checkboxes as $field) {
                if (!isset($fields[$field])) {
                    $fields[$field] = false;
                }
            }
        }

        if (property_exists($this->model, 'nullable')) {
            foreach ($this->model->nullable as $field) {
                if (!isset($fields[$field])) {
                    $fields[$field] = null;
                }
            }
        }

        foreach ($fields as $key => $value) {
            if (is_array($value) && empty($value)) {
                $fields[$key] = null;
            }
            if ($value === '') {
                $fields[$key] = null;
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

    public function afterSave($object, $fields)
    {
        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'afterSave' . class_basename($trait))) {
                $this->$method($object, $fields);
            }
        }
    }

    public function getFormFields($object)
    {
        $fields = $object->toArray();

        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'getFormFields' . class_basename($trait))) {
                $fields = $this->$method($object, $fields);
            }
        }

        return $fields;
    }

    public function getOldFormFieldsOnCreate()
    {
        $object = new $this->model();
        return $this->getFormFields($object);
    }

    public function filter($query, array $scopes = [])
    {
        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'filter' . class_basename($trait))) {
                $this->$method($query, $scopes);
            }
        }

        foreach ($scopes as $column => $value) {
            if (is_array($value)) {
                $query->whereIn($column, $value);
            } elseif ($column[0] == '%') {
                $value[0] == '!' ? $query->where(substr($column, 1), 'not like', '%' . substr($value, 1) . '%') : $query->where(substr($column, 1), 'like', '%' . $value . '%');
            } elseif ($value[0] == '!') {
                $query->where($column, '<>', substr($value, 1));
            } else {
                $query->where($column, $value);
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

    public function getFormFieldsForMultiSelect($fields, $relation, $attribute = 'id')
    {
        if (isset($fields[$relation])) {
            $list = [];
            foreach ($fields[$relation] as $value) {
                $list[$value['id']] = $value[$attribute];
            }

            $fields[$relation] = $list;
        }

        return $fields;
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
            $query->where($scopeField, 'like', '%' . $scopes[$scopeField] . '%');
            unset($scopes[$scopeField]);
        }
    }

    public function searchIn($query, &$scopes, $scopeField, $orFields = [])
    {
        if (isset($scopes[$scopeField]) && is_string($scopes[$scopeField])) {
            foreach ($orFields as $field) {
                $query->orWhere($field, 'like', '%' . $scopes[$scopeField] . '%');
            }
            unset($scopes[$scopeField]);
        }
    }

    public function isUniqueFeature()
    {
        return false;
    }

}
