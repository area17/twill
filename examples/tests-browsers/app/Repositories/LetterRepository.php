<?php

namespace App\Repositories;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Letter;

class LetterRepository extends ModuleRepository
{
    use HandleRevisions;

    public function __construct(Letter $model)
    {
        $this->model = $model;
    }

    public function afterSave(TwillModelContract $model, array $fields): void
    {
        $this->updateBrowser($model, $fields, 'writers');
        parent::afterSave($model, $fields);
    }

    public function getFormFields(TwillModelContract $object): array
    {
        $fields = parent::getFormFields($object);
        $fields['browsers']['writers'] = $this->getFormFieldsForBrowser($object, 'writers');
        return $fields;
    }
}
