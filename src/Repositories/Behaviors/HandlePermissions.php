<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Repositories\UserRepository;

trait HandlePermissions
{
    public function getFormFieldsHandlePermissions($object, $fields)
    {
        //render the each user's permission towards a specifically guide
        $users = app()->make(UserRepository::class)->get(["permissions" => function ($query) use ($object) {
            $query->where([['permissionable_type', get_class($object)], ['permissionable_id', $object->id]]);
        }]);

        foreach($users as $user) {
            $permission = $user->permissions->first();
            $fields['user_' . $user->id . '_permission'] = $permission ? "'" . $permission->guard_name . "'" : "";
        }

        return $fields;
    }
}
