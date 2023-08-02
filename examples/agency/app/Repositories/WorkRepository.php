<?php

namespace App\Repositories;

use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleBrowsers;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleRelatedBrowsers;
use A17\Twill\Repositories\Behaviors\HandleTags;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Work;

class WorkRepository extends ModuleRepository
{
    use HandleBlocks,
        HandleTranslations,
        HandleSlugs,
        HandleMedias,
        HandleRevisions,
        HandleFiles,
        HandleTags;

    protected $relatedBrowsers = ['offices'];

    public function __construct(Work $model)
    {
        $this->model = $model;
    }

    public function afterSave(TwillModelContract $object, array $fields): void
    {
        parent::afterSave($object, $fields);
        $object->sectors()->sync($fields['sectors'] ?? []);
        $object->disciplines()->sync($fields['disciplines'] ?? []);
        $this->updateRepeater(
            $object,
            $fields,
            'workLinks',
            'WorkLink',
            'external_link'
        );
        $this->updateBrowser($object, $fields, 'people');
    }

    public function getFormFields(TwillModelContract $object): array
    {
        $fields = parent::getFormFields($object);
        $fields = $this->getFormFieldsForRepeater($object, $fields, 'workLinks', 'WorkLink', 'external_link');
        $fields['browsers']['people'] = $this->getFormFieldsForBrowser($object, 'people', 'about');
        return $fields;
    }

    public function getWorks($with = [], $scopes = [], $orders = [], $relation = [], $perPage = 20, $forcePagination = false)
    {
        $query = $this->model->with($with);

        if (! empty($relation)) {
            $query->whereHas($relation['name'], function ($query) use ($relation) {
                $query->forSlug($relation['slug']);
            });
        }

        $query = $this->filter($query, $scopes);
        $query = $this->order($query, $orders);

        if (! $forcePagination && $this->model instanceof Sortable) {
            return $query->ordered()->get();
        }

        if ($perPage == -1) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    public function hydrate(TwillModelContract $object, array $fields): TwillModelContract
    {
        $this->hydrateMultiSelect($object, $fields, 'disciplines');
        $this->hydrateMultiSelect($object, $fields, 'sectors');
        return parent::hydrate($object, $fields);
    }
}
