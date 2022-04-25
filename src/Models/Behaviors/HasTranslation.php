<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Facades\TwillCapsules;
use Astrotomic\Translatable\Translatable;
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

    /**
     * @param string|null $locale
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public function scopeWithActiveTranslations(\Illuminate\Database\Eloquent\Builder $query, $locale = null)
    {
        if (method_exists($query->getModel(), 'translations')) {
            $locale = $locale == null ? app()->getLocale() : $locale;

            $query->whereHas('translations', function ($query) use ($locale): void {
                $query->whereActive(true);
                $query->whereLocale($locale);

                if (config('translatable.use_property_fallback', false)) {
                    $query->orWhere('locale', config('translatable.fallback_locale'));
                }
            });

            return $query->with(['translations' => function ($query) use ($locale): void {
                $query->whereActive(true);
                $query->whereLocale($locale);

                if (config('translatable.use_property_fallback', false)) {
                    $query->orWhere('locale', config('translatable.fallback_locale'));
                }
            }]);
        }
    }

    /**
     * @param string|null $locale
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByTranslation(\Illuminate\Database\Eloquent\Builder $query, string $orderField, string $orderType = 'ASC', $locale = null)
    {
        $translationTable = $this->getTranslationsTable();
        $localeKey = $this->getLocaleKey();
        $table = $this->getTable();
        $keyName = $this->getKeyName();
        $locale = $locale == null ? app()->getLocale() : $locale;

        return $query
            ->join($translationTable, function (JoinClause $join) use ($translationTable, $localeKey, $table, $keyName): void {
                $join
                    ->on($translationTable.'.'.$this->getRelationKey(), '=', $table.'.'.$keyName)
                    ->where($translationTable.'.'.$localeKey, $this->locale());
            })
            ->where($translationTable.'.'.$this->getLocaleKey(), $locale)
            ->orderBy($translationTable.'.'.$orderField, $orderType)
            ->select($table.'.*')
            ->with('translations');
    }

    /**
     * @param string|null $locale
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByRawByTranslation(\Illuminate\Database\Eloquent\Builder $query, string $orderRawString, string $groupByField, $locale = null)
    {
        $translationTable = $this->getTranslationsTable();
        $table = $this->getTable();
        $locale = $locale == null ? app()->getLocale() : $locale;

        return $query->join(sprintf('%s as t', $translationTable), sprintf('t.%s', $this->getRelationKey()), "=", sprintf('%s.id', $table))
            ->where($this->getLocaleKey(), $locale)
            ->groupBy(sprintf('%s.id', $table))
            ->groupBy(sprintf('t.%s', $groupByField))
            ->select(sprintf('%s.*', $table))
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
        return Collection::make(getLocales())->map(function ($locale): array {
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
     * @return Illuminate\Support\Collection
     */
    public function translatedAttribute(string $key)
    {
        return $this->translations->mapWithKeys(function ($translation) use ($key): array {
            return [$translation->locale => $this->translate($translation->locale)->$key];
        });
    }

}
