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
        foreach ($this->fieldsGroups as $group => $groupFields) {
            $fields[$group] = json_encode(Arr::only($fields, $groupFields));
            Arr::forget($fields, $groupFields);
        }

        return $fields;
    }

    /**
     * @param \A17\Twill\Models\Model|null $object
     * @param array $fields
     * @return array
     */
    public function prepareFieldsBeforeCreateHandleFieldsGroups($object, $fields)
    {
        foreach ($this->fieldsGroups as $group => $groupFields) {
            $fields[$group] = json_encode(Arr::only($fields, $groupFields));
            Arr::forget($fields, $groupFields);
        }

        return $fields;
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
                $fields = array_merge($fields, json_decode($object->$group, true));
            }
        }

        return $fields;
    }
}
