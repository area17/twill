<?php

namespace A17\CmsToolkit\Models\Behaviors;

use Dimsav\Translatable\Translatable;

trait HasTranslation
{
    use Translatable;

    public function getTranslationModelNameDefault()
    {
        return "App\Models\Translations\\" . class_basename($this) . 'Translation';
    }

    public function scopeWithActiveTranslations($query, $locale = null)
    {
        if (method_exists($query->getModel(), 'translations')) {
            $locale = $locale == null ? app()->getLocale() : $locale;

            $query->whereHas('translations', function ($query) use ($locale) {
                $query->whereActive(true);
                $query->whereLocale($locale);
            });

            return $query->with(['translations' => function ($query) use ($locale) {
                $query->whereActive(true);
                $query->whereLocale($locale);
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

        foreach ($this->translations as $translation) {
            if ($translation->getAttribute($this->getLocaleKey()) == $locale && $translation->getAttribute('active')) {
                return true;
            }
        }

        return false;
    }

}
