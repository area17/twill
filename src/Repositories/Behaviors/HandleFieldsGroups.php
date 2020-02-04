<?php

namespace A17\Twill\Repositories\Behaviors;

use Illuminate\Support\Arr;

trait HandleFieldsGroups
{

    /**
     * @param \A17\Twill\Models\Model|null $object
     * @param array $fields
     * @return array
     */
    public function prepareFieldsBeforeSaveHandleFieldsGroups($object, $fields)
    {
        return $this->handleFieldsGroups($fields);
    }

    /**
     * @param \A17\Twill\Models\Model|null $object
     * @param array $fields
     * @return array
     */
    public function prepareFieldsBeforeCreateHandleFieldsGroups($fields)
    {
        return $this->handleFieldsGroups($fields);
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleFieldsGroups($object, $fields)
    {
        foreach ($this->fieldsGroups as $group => $groupFields) {
            if ($object->$group) {
                $decoded_fields = json_decode($object->$group, true);
                // In case that some field read the value through $item->$name
                foreach($decoded_fields as $field_name => $field_value) {
                    $object->setAttribute($field_name, $field_value);
                }
                $fields = array_merge($fields, $decoded_fields);
            }
        }

        return $fields;
    }

    protected function handleFieldsGroups($fields) {
        foreach ($this->fieldsGroups as $group => $groupFields) {
            $fields[$group] = Arr::where(Arr::only($fields, $groupFields), function ($value, $key) {
                return !empty($value);
            });
            Arr::forget($fields, $groupFields);
        }

        return $fields;
    }
}
