<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Person;

class PersonRepository extends ModuleRepository
{
    use HandleBlocks, HandleTranslations, HandleSlugs, HandleMedias, HandleRevisions;

    public function __construct(Person $model)
    {
        $this->model = $model;
    }

    public function afterSave($object, $fields)
    {
        $this->updateRepeater($object, $fields, 'videos', 'PersonVideo', 'video');
        parent::afterSave($object, $fields);
        $this->updateBrowser($object, $fields, 'works');
    }

    public function getFormFields($object)
    {
        $fields = parent::getFormFields($object);
        $fields = $this->getFormFieldsForRepeater($object, $fields, 'videos', 'PersonVideo', 'video');
        $fields['browsers']['works'] = $this->getFormFieldsForBrowser($object, 'works', 'work');
        return $fields;
    }

    public function hydrate($object, $fields)
    {
        $this->hydrateRepeater($object, $fields, 'videos', 'PersonVideo', 'video');

        $this->hydrateBrowser($object, $fields, 'works');

        return parent::hydrate($object, $fields);
    }
}
