<?php

namespace A17\Twill\Repositories\Behaviors;

trait HandleSlugs
{
    public function afterSaveHandleSlugs($object, $fields)
    {
        if (property_exists($this->model, 'slugAttributes')) {
            foreach (getLocales() as $locale) {
                if (isset($fields['slug']) && isset($fields['slug'][$locale]) && !empty($fields['slug'][$locale])) {
                    $object->disableLocaleSlugs($locale);
                    $currentSlug = [];
                    $currentSlug['slug'] = $fields['slug'][$locale];
                    $currentSlug['locale'] = $locale;
                    $currentSlug['active'] = property_exists($this->model, 'translatedAttributes') ? $object->translate($locale)->active : 1;
                    $currentSlug = $this->getSlugParameters($object, $fields, $currentSlug);
                    $object->updateOrNewSlug($currentSlug);
                }
            }
        }
    }

    public function afterDeleteHandleSlugs($object)
    {
        $object->slugs()->delete();
    }

    public function afterRestoreHandleSlugs($object)
    {
        $object->slugs()->restore();
    }

    public function getFormFieldsHandleSlugs($object, $fields)
    {
        unset($fields['slugs']);

        if ($object->slugs != null) {
            foreach ($object->slugs as $slug) {
                if ($slug->active || $object->slugs->where('locale', $slug->locale)->where('active', true)->count() === 0) {
                    $fields['translations']['slug'][$slug->locale] = $slug->slug;
                }
            }
        }

        return $fields;
    }

    public function getSlugParameters($object, $fields, $slug)
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

    public function forSlug($slug, $with = [], $withCount = [], $scopes = [])
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

        if (!$item && config('translatable.use_property_fallback', false)) {
            $item = (clone $query)->orWhere(function ($query) {
                return $query->withActiveTranslations(config('translatable.fallback_locale'));
            })->forFallbackLocaleSlug($slug)->first();

            if ($item) {
                $item->redirect = true;
            }
        }

        return $item;
    }

    public function forSlugPreview($slug, $with = [], $withCount = [])
    {
        return $this->model->forInactiveSlug($slug)->with($with)->withCount($withCount)->first();
    }
}
