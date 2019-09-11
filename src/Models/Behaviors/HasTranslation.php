<?php

namespace A17\Twill\Models\Behaviors;

use Dimsav\Translatable\Translatable;

trait HasTranslation
{
    use Translatable;

    public function getTranslationModelNameDefault()
    {
        return config('twill.namespace') . "\Models\Translations\\" . class_basename($this) . 'Translation';
    }

    public function scopeWithActiveTranslations($query, $locale = null)
    {
        if (method_exists($query->getModel(), 'translations')) {
            $locale = $locale == null ? app()->getLocale() : $locale;

            $query->whereHas('translations', function ($query) use ($locale) {
                $query->whereActive(true);
                $query->whereLocale($locale);

                if (config('translatable.use_property_fallback', false)) {
                    $query->orWhere('locale', config('translatable.fallback_locale'));
                }
            });

            return $query->with(['translations' => function ($query) use ($locale) {
                $query->whereActive(true);
                $query->whereLocale($locale);

                if (config('translatable.use_property_fallback', false)) {
                    $query->orWhere('locale', config('translatable.fallback_locale'));
                }
            }]);
        }
    }

    public function scopeOrderByTranslation($query, $orderField, $orderType = 'ASC', $locale = null)
    {
        $translationTable = $this->getTranslationsTable();
        $table = $this->getTable();
        $locale = $locale == null ? app()->getLocale() : $locale;

        return $query->join("{$translationTable} as t", "t.{$this->getRelationKey()}", "=", "{$table}.id")
            ->where($this->getLocaleKey(), $locale)
            ->groupBy("{$table}.id")
            ->groupBy("t.{$orderField}")
            ->select("{$table}.*")
            ->orderBy("t.{$orderField}", $orderType)
            ->with('translations');
    }

    public function scopeOrderByRawByTranslation($query, $orderRawString, $groupByField, $locale = null)
    {
        $translationTable = $this->getTranslationsTable();
        $table = $this->getTable();
        $locale = $locale == null ? app()->getLocale() : $locale;

        return $query->join("{$translationTable} as t", "t.{$this->getRelationKey()}", "=", "{$table}.id")
            ->where($this->getLocaleKey(), $locale)
            ->groupBy("{$table}.id")
            ->groupBy("t.{$groupByField}")
            ->select("{$table}.*")
            ->orderByRaw($orderRawString)
            ->with('translations');
    }

    public function hasActiveTranslation($locale = null)
    {
        $locale = $locale ?: $this->locale();

        $translations = $this->memoizedTranslations ?? ($this->memoizedTranslations = $this->translations()->get());

        foreach ($translations as $translation) {
            if ($translation->getAttribute($this->getLocaleKey()) == $locale && $translation->getAttribute('active')) {
                return true;
            }
        }

        return false;
    }

    public function getActiveLanguages()
    {
        return $this->translations->map(function ($translation) {
            return [
                'shortlabel' => strtoupper($translation->locale),
                'label' => getLanguageLabelFromLocaleCode($translation->locale),
                'value' => $translation->locale,
                'published' => $translation->active ?? false,
            ];
        })->sortBy(function ($translation) {
            $localesOrdered = config('translatable.locales');
            return array_search($translation['value'], $localesOrdered);
        })->values();
    }

    public function translatedAttribute($key)
    {
        return $this->translations->mapWithKeys(function ($translation) use ($key) {
            return [$translation->locale => $this->translate($translation->locale)->$key];
        });
    }

}
