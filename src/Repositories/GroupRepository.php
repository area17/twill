<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Group;
use A17\Twill\Repositories\Behaviors\HandleGroupPermissions;

class GroupRepository extends ModuleRepository
{
    use HandleGroupPermissions;

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

    public function delete($id)
    {
        if ($this->model->find($id)->is_everyone_group) {
            return false;
        }

        return parent::delete($id);
    }

    public function bulkDelete($ids)
    {
        $includes_everyone_group = $this->model->whereIn('id', $ids)
            ->where('is_everyone_group', true)
            ->first();

        if ($includes_everyone_group) {
            return false;
        }

        return parent::bulkDelete($ids);
    }
}
