<?php

namespace A17\Twill\Repositories;

use Illuminate\Support\Arr;
use A17\Twill\Models\Group;
use A17\Twill\Models\User;

class GroupRepository extends ModuleRepository
{

    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    public function getFormFields($group)
    {
        $fields = parent::getFormFields($group);

        $fields['browsers']['users'] = $this->getFormFieldsForBrowser($group, 'users');

        return $fields;
    }

    public function afterSave($group, $fields)
    {
        $this->updateBrowser($group, $fields, 'users');

        parent::afterSave($group, $fields);
    }
}
