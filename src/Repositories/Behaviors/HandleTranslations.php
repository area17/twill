<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HandleTranslations
{
    public function prepareFieldsBeforeCreateHandleTranslations(array $fields): array
    {
        return $this->prepareFieldsBeforeSaveHandleTranslations(null, $fields);
    }

    public function prepareFieldsBeforeSaveHandleTranslations(?TwillModelContract $object, array $fields): array
    {
        if ($this->model->isTranslatable()) {
            $locales = getLocales();
            $localesCount = count($locales);
            $attributes = Collection::make($this->model->translatedAttributes);

            $submittedLanguages = Collection::make($fields['languages'] ?? []);

            $atLeastOneLanguageIsPublished = $submittedLanguages->contains(function ($language) {
                return $language['published'];
            });

            foreach ($locales as $index => $locale) {
                $submittedLanguage = Arr::first($submittedLanguages->filter(function ($lang) use ($locale) {
                    return $lang['value'] === $locale;
                }));

                $shouldPublishFirstLanguage = ($index === 0 && !$atLeastOneLanguageIsPublished);

                $fallBack = $fields[$locale]['active'] ?? false;

                $activeField = $shouldPublishFirstLanguage || ($submittedLanguage['published'] ?? $fallBack);

                $fields[$locale] = [
                        'active' => $activeField,
                    ] + $attributes->mapWithKeys(function ($attribute) use (&$fields, $locale, $localesCount, $index) {
                        $attributeValue = $fields[$attribute] ?? null;

                        // if we are at the last locale,
                        // let's unset this field as it is now managed by this trait
                        if ($index + 1 === $localesCount) {
                            unset($fields[$attribute]);
                        }

                        return [
                            $attribute => ($attributeValue[$locale] ?? $fields[$locale][$attribute] ?? null),
                        ];
                    })->toArray();
            }

            unset($fields['languages']);
        }

        return $fields;
    }

    public function getFormFieldsHandleTranslations(TwillModelContract $object, array $fields): array
    {
        // Keep a copy of the slugs to add it again after.
        $slug = $fields['translations']['slug'] ?? null;
        unset($fields['translations']);

        if ($object->translations !== null && $object->translatedAttributes != null) {
            foreach ($object->translations as $translation) {
                foreach ($object->translatedAttributes as $attribute) {
                    unset($fields[$attribute]);
                    if (array_key_exists($attribute, $this->fieldsGroups) && is_array($translation->{$attribute})) {
                        foreach ($this->fieldsGroups[$attribute] as $field_name) {
                            if (isset($translation->{$attribute}[$field_name])) {
                                if ($this->fieldsGroupsFormFieldNamesAutoPrefix) {
                                    $fields['translations'][$attribute . $this->fieldsGroupsFormFieldNameSeparator . $field_name][$translation->locale] = $translation->{$attribute}[$field_name];
                                } else {
                                    $fields['translations'][$field_name][$translation->locale] = $translation->{$attribute}[$field_name];
                                }
                            }
                        }

                        unset($fields['translations'][$attribute]);
                    } else {
                        $fields['translations'][$attribute][$translation->locale] = $translation->{$attribute};
                    }
                }
            }
        }

        if ($slug) {
            $fields['translations']['slug'] = $slug;
        }

        return $fields;
    }

    public function orderHandleTranslations(Builder $query, array &$orders): void
    {
        if ($this->model->isTranslatable()) {
            $attributes = $this->model->translatedAttributes;
            $table = $this->model->getTable();
            $tableTranslation = $this->model->translations()->getRelated()->getTable();
            $foreignKeyMethod = method_exists($this->model->translations(), 'getQualifiedForeignKeyName') ? 'getQualifiedForeignKeyName' : 'getForeignKey';
            $foreignKey = $this->model->translations()->$foreignKeyMethod();

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
                    ->select($table . '.*');
            }
        }
    }

    public function getPublishedScopesHandleTranslations(): array
    {
        return ['withActiveTranslations'];
    }
}
