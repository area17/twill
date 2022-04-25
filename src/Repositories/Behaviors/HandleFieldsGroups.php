<?php

namespace A17\Twill\Repositories\Behaviors;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HandleFieldsGroups
{
    /**
     * @return \A17\Twill\Models\Model
     */
    public function hydrateHandleFieldsGroups(\A17\Twill\Models\Model $object, ?array $fields)
    {
        foreach ($this->fieldsGroups as $group => $groupFields) {
            if ($object->$group) {
                $casts = $this->getModelCasts($object);
                if (!array_key_exists($group, $casts) || (array_key_exists($group, $casts) && $casts[$group] !== 'array')) {
                    $object->$group = json_encode($object->$group);
                }
            }
        }

        return $object;
    }

    /**
     * @return array
     * @param mixed[] $fields
     */
    public function prepareFieldsBeforeSaveHandleFieldsGroups(?\A17\Twill\Models\Model $object, array $fields)
    {
        return $this->handleFieldsGroups($fields);
    }

    /**
     * @return array
     * @param mixed[] $fields
     */
    public function prepareFieldsBeforeCreateHandleFieldsGroups(array $fields)
    {
        return $this->handleFieldsGroups($fields);
    }

    /**
     * @return array
     * @param mixed[] $fields
     */
    public function getFormFieldsHandleFieldsGroups(\A17\Twill\Models\Model $object, array $fields)
    {
        foreach ($this->fieldsGroups as $group => $groupFields) {
            if ($object->$group) {
                $casts = $this->getModelCasts($object);
                if (array_key_exists($group, $casts) && $casts[$group] === 'array') {
                    $decoded_fields = $object->$group;
                } else {
                    $decoded_fields = json_decode($object->$group, true) ?? [];
                }

                // In case that some field read the value through $item->$name
                foreach ($decoded_fields as $field_name => $field_value) {
                    if ($this->fieldsGroupsFormFieldNamesAutoPrefix) {
                        $decoded_fields[$group . $this->fieldsGroupsFormFieldNameSeparator . $field_name] = $field_value;
                        unset($decoded_fields[$field_name]);

                        if (!is_array($field_value)) {
                            $object->setAttribute($group . $this->fieldsGroupsFormFieldNameSeparator . $field_name, $field_value);
                        }
                    } else {
                        $object->setAttribute($field_name, $field_value);
                    }
                }

                $fields = array_merge($fields, $decoded_fields);
            }
        }

        return $fields;
    }

    protected function handleFieldsGroups($fields)
    {
        foreach ($this->fieldsGroups as $group => $groupFields) {
            if ($this->fieldsGroupsFormFieldNamesAutoPrefix) {
                $groupFields = array_map(function ($field_name) use ($group): string {
                    return $group . $this->fieldsGroupsFormFieldNameSeparator . $field_name;
                }, $groupFields);
            }

            $fields[$group] = Arr::where(Arr::only($fields, $groupFields), function ($value, $key): bool {
                return !empty($value);
            });

            if ($this->fieldsGroupsFormFieldNamesAutoPrefix) {
                $fieldsGroupWithGroupSeparator = [];
                foreach ($fields[$group] as $key => $value) {
                    $fieldsGroupWithGroupSeparator[Str::replaceFirst($group . $this->fieldsGroupsFormFieldNameSeparator, '', $key)] = $value;
                }

                $fields[$group] = $fieldsGroupWithGroupSeparator;
            }

            if (in_array($group, $this->model->getTranslatedAttributes()) && is_array($fields[$group])) {
                $fieldForTranslationTrait = [];
                foreach ($fields[$group] as $field => $translatedValues) {
                    foreach ($translatedValues as $locale => $value) {
                        $fieldForTranslationTrait[$locale][$field] = $value;
                    }
                }

                $fields[$group] = $fieldForTranslationTrait;
            }

            if (empty($fields[$group])) {
                $fields[$group] = null;
            }

            Arr::forget($fields, $groupFields);
        }

        return $fields;
    }

    /**
     * @return array
     */
    protected function getModelCasts(\A17\Twill\Models\Model $object)
    {
        $casts = $object->getCasts();
        if ($this->model->isTranslatable()) {
            $casts += app()->make($this->model->getTranslationModelNameDefault())->getCasts();
        }

        return $casts;
    }
}
