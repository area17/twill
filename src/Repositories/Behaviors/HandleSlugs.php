<?php

namespace A17\Twill\Repositories\Behaviors;

trait HandleSlugs
{
    /**
     * @return void
     * @param mixed[] $fields
     */
    public function afterSaveHandleSlugs(\A17\Twill\Models\Model $object, array $fields)
    {
        if (property_exists($this->model, 'slugAttributes')) {
            foreach (getLocales() as $locale) {
                if (isset($fields['slug']) && isset($fields['slug'][$locale]) && !empty($fields['slug'][$locale])) {
                    $object->disableLocaleSlugs($locale);
                    $currentSlug = [];
                    $currentSlug['slug'] = $fields['slug'][$locale];
                    $currentSlug['locale'] = $locale;
                    $currentSlug['active'] = $this->model->isTranslatable() ? $object->translate($locale)->active : 1;
                    $currentSlug = $this->getSlugParameters($object, $fields, $currentSlug);
                    $object->updateOrNewSlug($currentSlug);
                }
            }
        }
    }

    /**
     * @return void
     */
    public function afterDeleteHandleSlugs(\A17\Twill\Models\Model $object)
    {
        $object->slugs()->delete();
    }

    /**
     * @return void
     */
    public function afterRestoreHandleSlugs(\A17\Twill\Models\Model $object)
    {
        $object->slugs()->restore();
    }

    /**
     * @return array
     * @param mixed[] $fields
     */
    public function getFormFieldsHandleSlugs(\A17\Twill\Models\Model $object, array $fields)
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

    /**
     * @return array
     * @param mixed[] $fields
     * @param mixed[] $slug
     */
    public function getSlugParameters(\A17\Twill\Models\Model $object, array $fields, array $slug)
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

    /**
     * @return \A17\Twill\Models\Model|null
     * @param mixed[] $with
     * @param mixed[] $withCount
     * @param mixed[] $scopes
     */
    public function forSlug(string $slug, array $with = [], array $withCount = [], array $scopes = [])
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

        if (!$item && config('translatable.use_property_fallback', false)
        && config('translatable.fallback_locale') != config('app.locale')) {
            $item = (clone $query)->orWhere(function ($query) {
                return $query->withActiveTranslations(config('translatable.fallback_locale'));
            })->forFallbackLocaleSlug($slug)->first();

            if ($item) {
                $item->redirect = true;
            }
        }

        return $item;
    }

    /**
     * @return \A17\Twill\Models\Model
     * @param mixed[] $with
     * @param mixed[] $withCount
     */
    public function forSlugPreview(string $slug, array $with = [], array $withCount = [])
    {
        return $this->model->forInactiveSlug($slug)->with($with)->withCount($withCount)->first();
    }
}
