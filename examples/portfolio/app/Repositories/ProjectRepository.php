<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Project;

class ProjectRepository extends ModuleRepository
{
    use HandleBlocks, HandleTranslations, HandleSlugs, HandleMedias, HandleRevisions;

    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    public function afterSave($model, $fields): void
    {
        $this->updateRepeaterMorphMany(
            $model,
            $fields,
            'comments',
            'commentable',
            'Comment',
            'comment'
        );

        $this->updateRepeater(
            $model,
            $fields,
            'links',
        );

        $this->updateRepeaterWithPivot(
            $model,
            $fields,
            'partners',
            ['role'],
            'Partner',
            'project_partner',
        );
        parent::afterSave($model, $fields);
    }

    public function getFormFields($object): array
    {
        $fields = parent::getFormFields($object);

        $fields = $this->getFormFieldsForRepeater(
            $object,
            $fields,
            'comments',
            'Comment',
            'comment'
        );

        $fields = $this->getFormFieldsForRepeater(
            $object,
            $fields,
            'links',
            'Link',
            'links'
        );

        return $this->getFormFieldForRepeaterWithPivot(
            $object,
            $fields,
            'partners',
            ['role'],
            'Partner',
            'project_partner'
        );
    }
}
