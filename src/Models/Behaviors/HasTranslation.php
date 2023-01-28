<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Commands\Build;
use A17\Twill\Facades\TwillCapsules;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

trait HasTranslation
{
    use Translatable;

    /**
     * Returns the fully qualified translation class name for this model.
     *
     * @return string|null
     */
    public function getTranslationModelNameDefault()
    {
        $repository = config('twill.namespace') . "\Models\Translations\\" . class_basename($this) . 'Translation';

        if (@class_exists($repository)) {
            return $repository;
        }

        return TwillCapsules::getCapsuleForModel(class_basename($this))->getTranslationModel();
    }

    public function scopeWithActiveTranslations(Builder $query, ?string $locale = null): Builder
    {
        if (method_exists($query->getModel(), 'translations')) {
            $locale = $locale ?? app()->getLocale();

            $query->whereHas('translations', function ($query) use ($locale) {
                $query->whereActive(true);
                $query->whereLocale($locale);

                if (config('translatable.use_fallback') && config('translatable.use_property_fallback', false)) {
                    $query->orWhere('locale', config('translatable.fallback_locale'));
                }
            });

            return $query->with(['translations' => function ($query) use ($locale) {
                $query->whereActive(true);
                $query->whereLocale($locale);

                if (config('translatable.use_fallback') && config('translatable.use_property_fallback', false)) {
                    $query->orWhere('locale', config('translatable.fallback_locale'));
                }
            }]);
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $orderField
     * @param string $orderType
     * @param string|null $locale
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByTranslation($query, $orderField, $orderType = 'ASC', $locale = null)
    {
        $translationTable = $this->getTranslationsTable();
        $localeKey = $this->getLocaleKey();
        $table = $this->getTable();
        $keyName = $this->getKeyName();
        $locale = $locale == null ? app()->getLocale() : $locale;

        return $query
            ->join($translationTable, function (JoinClause $join) use ($translationTable, $localeKey, $table, $keyName) {
                $join
                    ->on($translationTable . '.' . $this->getRelationKey(), '=', $table . '.' . $keyName)
                    ->where($translationTable . '.' . $localeKey, $this->locale());
            })
            ->where($translationTable . '.' . $this->getLocaleKey(), $locale)
            ->orderBy($translationTable . '.' . $orderField, $orderType)
            ->select($table . '.*')
            ->with('translations');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $orderRawString
     * @param string $groupByField
     * @param string|null $locale
     * @return \Illuminate\Database\Eloquent\Builder
     */
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

    /**
     * Checks if this model has active translations.
     *
     * @param string|null $locale
     * @return bool
     */
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

    /**
     * @return Illuminate\Support\Collection
     */
    public function getActiveLanguages()
    {
        return Collection::make(getLocales())->map(function ($locale) {
            $translation = $this->translations->firstWhere('locale', $locale);

            return [
                'shortlabel' => strtoupper($locale),
                'label' => getLanguageLabelFromLocaleCode($locale),
                'value' => $locale,
                'published' => $translation->active ?? false,
            ];
        })->values();
    }

    /**
     * Returns all translations for a given attribute.
     *
     * @param string $key
     * @return Illuminate\Support\Collection
     */
    public function translatedAttribute($key)
    {
        return $this->translations->mapWithKeys(function ($translation) use ($key) {
            return [$translation->locale => $this->translate($translation->locale)->$key];
        });
    }
}
