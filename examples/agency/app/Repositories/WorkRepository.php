<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleTags;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Work;

class WorkRepository extends ModuleRepository
{
    use HandleBlocks, HandleTranslations, HandleSlugs, HandleMedias, HandleRevisions, HandleFiles, HandleTags;

    protected $relatedBrowsers = ['offices'];

    public function __construct(Work $model)
    {
        $this->model = $model;
    }

    public function afterSave($object, $fields)
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

    public function getFormFields($object)
    {
        $fields = parent::getFormFields($object);
        $fields = $this->getFormFieldsForRepeater($object, $fields, 'workLinks', 'WorkLink', 'external_link');
        $fields['browsers']['people'] = $this->getFormFieldsForBrowser($object, 'people', 'about');
        return $fields;
    }
}
