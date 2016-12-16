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

            $query->with(['translations' => function ($query) use ($locale) {
                $query->whereActive(true);
                $query->whereLocale($locale);
            }]);
        }
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
