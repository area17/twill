<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HandleSlugs
{
    public function beforeSaveHandleSlugs(TwillModelContract $object, array $fields): void
    {
        if (property_exists($this->model, 'slugAttributes')) {
            $object->twillSlugData = [];
            foreach (getLocales() as $locale) {
                if (isset($fields['slug'][$locale]) && !empty($fields['slug'][$locale])) {
                    $currentSlug = [];
                    $currentSlug['slug'] = $fields['slug'][$locale];
                    $currentSlug['locale'] = $locale;
                    $currentSlug['active'] = $this->model->isTranslatable() ? $object->translate($locale)->active : true;
                    $currentSlug = $this->getSlugParameters($object, $fields, $currentSlug);
                    $object->twillSlugData[] = $currentSlug;
                } else {
                    $slugParams = $this->model->slugAttributes;
                    $slugData = [];

                    foreach ($slugParams as $param) {
                        $slugData[] = $fields[$param][$locale] ?? '';
                    }

                    if (!empty(Arr::join($slugData, '-'))) {
                        $object->twillSlugData[] = [
                            'slug' => Str::slug(Arr::join($slugData, '-')),
                            'active' => $this->model->isTranslatable() ? $object->translate($locale)->active : 1,
                            'locale' => $locale
                        ];
                    }
                }
            }
        }
    }

    public function afterDeleteHandleSlugs(TwillModelContract $object): void
    {
        $object->slugs()->delete();
    }

    public function afterRestoreHandleSlugs(TwillModelContract $object): void
    {
        $object->slugs()->restore();
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

    public function getSlugParameters(TwillModelContract $object, array $fields, array $slug): array
    {
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
