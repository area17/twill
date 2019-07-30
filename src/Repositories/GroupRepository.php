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

    //When the user is added to a new group, grant the user all permissions the group has.
    public function updateBrowser($group, $fields, $relationship, $positionAttribute = 'position')
    {
        // When the user is added into / removed from a group, grant / revoke all permissions that group has.
        if ($relationship === 'users' && isset($fields['browsers']['users'])) { //No need to update users if no browser is present
            $currentUsersIds = $group->users->pluck('id')->toArray();
            $newUsersIds = Arr::pluck($fields['browsers']['users'], 'id');

            $addedUsersIds = array_values(array_diff($newUsersIds, $currentUsersIds));
            $deletedUsersIds = array_values(array_diff($currentUsersIds, $newUsersIds));
            $viewableItems = $group->viewableItems();

            if (!empty($addedUsersIds)) {
                $addedUsers = User::whereIn('id', $addedUsersIds)->get();

                foreach($addedUsers as $user) {
                    foreach($viewableItems as $item) {
                        if (!$user->permissions()->ofItem($item)->first()) {
                            $user->grantModuleItemPermission('view-item', $item);
                        }
                    }
                }
            }

            if (!empty($deletedUsersIds)) {
                $deletedUsers = User::whereIn('id', $deletedUsersIds)->get();

                foreach($deletedUsers as $user) {
                    foreach($viewableItems as $item) {
                        $userPermission = $user->permissions()->ofItem($item)->first();
                        if ($userPermission && $userPermission->name === 'view-item') {
                            $user->revokeModuleItemAllPermissions($item);
                        }
                    }
                }
            }
        }

        parent::updateBrowser($group, $fields, $relationship);
    }
}
