<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Group;
use A17\Twill\Repositories\Behaviors\HandlePermissions;

class GroupRepository extends ModuleRepository
{
    use HandlePermissions;

    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    public function getFormFields($object)
    {
        $fields = parent::getFormFields($object);

        $fields['browsers']['users'] = $this->getFormFieldsForBrowser($object, 'users');

        return $fields;
    }

    public function afterSave($object, $fields)
    {
        $this->updateBrowser($object, $fields, 'users');

        parent::afterSave($object, $fields);
    }
}
