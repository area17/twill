<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HandleSlugs
{
    public function beforeSaveHandleSlugs(TwillModelContract $object, array $fields): void
    {
        if (method_exists($this->model, 'getSlugFields')) {
            $object->twillSlugData = [];
            $submittedLanguages = Collection::make($fields['languages'] ?? []);

            $atLeastOneLanguageIsPublished = $submittedLanguages->contains(function ($language) {
                return $language['published'];
            });
            foreach (getLocales() as $index => $locale) {
                $submittedLanguage = $submittedLanguages->first(function ($lang) use ($locale) {
                    return $lang['value'] === $locale;
                });

                $shouldPublishFirstLanguage = ($index === 0 && !$atLeastOneLanguageIsPublished);

                $fallBack = $fields[$locale]['active'] ?? false;

                // Copy active fallback behavior from HandleTranslations
                $activeField = $shouldPublishFirstLanguage || ($submittedLanguage['published'] ?? $fallBack);

                $currentSlug = [];
                $currentSlug['locale'] = $locale;
                $currentSlug['active'] = $activeField;
                if (!empty($fields['slug'][$locale])) {
                    $currentSlug['slug'] = $fields['slug'][$locale];
                }
                $object->twillSlugData[$locale] = $currentSlug;
            }
        }
    }

    public function getFormFieldsHandleSlugs(TwillModelContract $model, array $fields): array
    {
        unset($fields['slugs']);

        if ($model->slugs !== null) {
            foreach ($model->slugs as $slug) {
                if ($slug->active || $model->slugs->where('locale', $slug->locale)->where('active', true)->count() === 0) {
                    $fields['translations']['slug'][$slug->locale] = $slug->slug;
                }
            }
        }

        return $fields;
    }

    /** @deprecated We merge twillSlugData with getSlugParams on save, to avoid getting outdated data */
    public function getSlugParameters(TwillModelContract $object, array $fields, array $slug): ?array
    {
        trigger_deprecation('area17/twill', '3.5', 'The getSlugParameters method is deprecated as it returns data before fields are applied and will be removed in 4.x');

        $slugParams = $object->getSlugParams($slug['locale']);

        foreach ($object->slugAttributes as $param) {
            if (isset($slugParams[$param]) && isset($fields[$param])) {
                $slug[$param] = $fields[$param];
            } elseif (isset($slugParams[$param])) {
                $slug[$param] = $slugParams[$param];
            }
        }
        return $slug;
    }

    public function forSlug(string $slug, array $with = [], array $withCount = [], array $scopes = []): ?TwillModelContract
    {
        $query = $this->model->where($scopes)->scopes(['published', 'visible']);

        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'getPublishedScopes' . class_basename($trait))) {
                $query->scopes($this->$method());
            }
        }

        $item = (clone $query)->forSlug($slug)->with($with)->withCount($withCount)->first();

        if (!$item && $item = (clone $query)->forInactiveSlug($slug)->first()) {
            $item->redirect = true;
        }

        if (
            !$item && config('translatable.use_property_fallback', false)
            && config('translatable.fallback_locale') != config('app.locale')
        ) {
            $item = (clone $query)->orWhere(function ($query) {
                return $query->withActiveTranslations(config('translatable.fallback_locale'));
            })->forFallbackLocaleSlug($slug)->first();

            if ($item) {
                $item->redirect = true;
            }
        }

        return $item;
    }

    public function forSlugPreview(string $slug, array $with = [], array $withCount = []): ?TwillModelContract
    {
        return $this->model->forInactiveSlug($slug)->with($with)->withCount($withCount)->first();
    }
}
