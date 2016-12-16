<?php

namespace A17\CmsToolkit\Repositories\Behaviors;

trait HandleTranslations
{
    protected $nullableFields = [];

    public function prepareFieldsBeforeSaveHandleTranslations($object, $fields)
    {
        if (property_exists($this->model, 'translatedAttributes')) {
            $locales = getLocales();
            foreach ($locales as $locale) {
                $translate = $object->translateOrNew($locale);
                $translate->active = $fields['active_' . $locale] ?? 0;
                foreach ($object->translatedAttributes as $field) {
                    if (array_key_exists("{$field}_{$locale}", $fields)) {
                        if (empty($fields["{$field}_{$locale}"])) {
                            $translate->{$field} = null;
                        } else {
                            $translate->{$field} = $fields["{$field}_{$locale}"];
                        }
                    } elseif (in_array($field, $this->nullableFields)) {
                        $translate->{$field} = null;
                    }
                }
            }
        }

        return $fields;
    }

    public function getFormFieldsHandleTranslations($object, $fields)
    {
        if ($object->translations != null && $object->translatedAttributes != null) {
            foreach ($object->translations as $translation) {
                foreach ($object->translatedAttributes as $value) {
                    $fields[$value . '_' . $translation->locale] = $translation->{$value};
                }
            }
        }

        return $fields;
    }

    protected function filterHandleTranslations($query, &$scopes)
    {
        if (property_exists($this->model, 'translatedAttributes')) {
            $attributes = $this->model->translatedAttributes;
            $query->whereHas('translations', function ($q) use ($scopes, $attributes) {
                foreach ($attributes as $attribute) {
                    if (isset($scopes[$attribute]) && is_string($scopes[$attribute])) {
                        $q->where($attribute, 'like', '%' . $scopes[$attribute] . '%');
                    }
                }
            });

            foreach ($attributes as $attribute) {
                if (isset($scopes[$attribute])) {
                    unset($scopes[$attribute]);
                }
            }
        }
    }

    public function orderHandleTranslations($query, &$orders)
    {
        if (property_exists($this->model, 'translatedAttributes')) {
            $attributes = $this->model->translatedAttributes;
            $table = $this->model->getTable();
            $tableTranslation = $this->model->translations()->getRelated()->getTable();
            $foreignKey = $this->model->translations()->getForeignKey();

            $isOrdered = false;
            foreach ($attributes as $attribute) {
                if (isset($orders[$attribute])) {
                    $query->orderBy($tableTranslation . '.' . $attribute, $orders[$attribute]);
                    $isOrdered = true;
                    unset($orders[$attribute]);
                }
            }

            if ($isOrdered) {
                $query
                    ->join($tableTranslation, $foreignKey, '=', $table . '.id')
                    ->where($tableTranslation . '.locale', '=', $orders['locale'] ?? app()->getLocale())
                    ->select($table . '.*')
                ;
            }
        }
    }
}
