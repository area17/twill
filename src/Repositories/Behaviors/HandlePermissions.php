<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Repositories\UserRepository;
use A17\Twill\Models\Permission;

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

    public function afterSaveHandlePermissions($object, $fields)
    {
        foreach($fields as $key => $value) {
            if(starts_with($key, 'user_') && ends_with($key, '_permission')) {
                $user_id = explode('_', $key)[1];
                $user = app()->make(UserRepository::class)->getById($user_id);
                
                $permission = $user->itemPermission($object) ?? new Permission;

                // Only value existed, do update or create
                if ($value) {
                    $permission->permission_name = $value;
                    $permission->guard_name = $value;
                    $permission->permissionable()->associate($object);
                    $user->permissions()->save($permission);
                    $permission->save();
                }
                // If the existed permission has been set as none, delete the origin permission
                elseif ($permission) {
                    $permission->delete();
                }
            }
        }        
    }
}
