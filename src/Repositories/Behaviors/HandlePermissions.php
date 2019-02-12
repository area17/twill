<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Permission;
use A17\Twill\Repositories\GroupRepository;
use A17\Twill\Repositories\UserRepository;

trait HandlePermissions
{
    public function getFormFieldsHandlePermissions($object, $fields)
    {
        // User form
        if (get_class($object) === "A17\Twill\Models\User") {
            foreach ($object->permissions as $permission) {
                $module = $permission->permissionable()->withoutGlobalScope('authorized')->first();
                $module_name = str_plural(lcfirst(class_basename($module)));
                $fields[$module_name . '_' . $module->id . '_permission'] = '"' . $permission->name . '"';
            }
        }
        // Module instance form
        else {
            $fields = $this->renderUserPermissions($object, $fields);
            $fields = $this->renderGroupPermissions($object, $fields);
        }

        return $fields;
    }

    // public function afterSaveHandlePermissions($object, $fields)
    // {
    //     foreach ($fields as $key => $value) {
    //         if (ends_with($key, '_permission')) {

    //             // Handle permissions fields on module item page
    //             if (starts_with($key, 'user')) {
    //                 $user_id = explode('_', $key)[1];
    //                 $user = app()->make(UserRepository::class)->getById($user_id);
    //                 $item = $object;
    //             }
    //             // Handle permissions fields on user page
    //             else {
    //                 $item_name = explode('_', $key)[0];
    //                 $item_id = explode('_', $key)[1];
    //                 $item = getRepositoryByModuleName($item_name)->getById($item_id);
    //                 $user = $object;
    //             }

    //             // Only value existed, do update or create
    //             if ($value) {
    //                 $permission = Permission::firstOrCreate([
    //                     'name' => $value,
    //                     'permissionable_type' => get_class($item),
    //                     'permissionable_id' => $item->id,
    //                 ]);
    //                 $user->permissions()->save($permission);
    //             }
    //             // If the existed permission has been set as none, delete the origin permission
    //             elseif ($user->itemPermission($item)) {
    //                 $user->permissions()->detach($user->itemPermission($item)->id);
    //             }
    //         } elseif (ends_with($key, '_group_authorized')) {
    //             $this->handleGroupPermissions($object, $fields, $key, $value);
    //         }
    //     }
    // }

    public function afterSaveHandlePermissions($object, $fields)
    {
        //User form page
        if (get_class($object) === "A17\Twill\Models\User") {
            $this->handleUserPermissions($object, $fields);
        }
        // Group form page
        elseif (get_class($object) === "A17\Twill\Models\Group") {
            $this->handleGroupPermissions($object, $fields);
        }
        // Role form page
        elseif (get_class($object) === "A17\Twill\Models\Role") {
            $this->handleRolePermissions($object, $fields);
        }
        // Module item form page
        else {
            $this->handleModulePermissions($object, $fields);
        }
    }

    // Render each user's permission under a item
    protected function renderUserPermissions($object, $fields)
    {
        $users = app()->make(UserRepository::class)->get(["permissions" => function ($query) use ($object) {
            $query->where([['permissionable_type', get_class($object)], ['permissionable_id', $object->id]]);
        }]);

        foreach ($users as $user) {
            $permission = $user->permissions->first();
            $fields['user_' . $user->id . '_permission'] = $permission ? "'" . $permission->name . "'" : "";
        }

        return $fields;
    }

    // Render each group's permission under a item
    protected function renderGroupPermissions($object, $fields)
    {
        $groups = app()->make(GroupRepository::class)->get(['users.permissions']);
        foreach ($groups as $group) {
            $fields[$group->id . '_group_authorized'] = $this->allUsersInGroupAuthorized($group, $object);
        }
        return $fields;
    }

    // After save handle permissions form fields on user form
    protected function handleUserPermissions($object, $fields)
    {

    }

    // After save handle permissions form fields on role form
    protected function handleRolePermissions($object, $fields)
    {
        foreach ($fields["general-permissions"] as $permissionName) {
            switch ($permissionName) {
                case "edit-property-settings":
                    $object->grantGlobalPermission("edit-settings");
                    break;
                case "manage-users":
                    $object->grantGlobalPermission("edit-users");
                    break;
                case "manage-user-roles":
                    $object->grantGlobalPermission("edit-user-roles");
                    break;
                case "manage-user-groups":
                    $object->grantGlobalPermission("edit-user-groups");
                    break;
            }
        }
    }

    // After save handle permissions form fields on module form
    protected function handleModulePermissions($object, $fields)
    {

    }

    // After save handle permissions form fields on group form:
    // If one group checked, switch the permission of all users in the group to at least view.
    // If one group unchecked, switch the permission of all users who have view permissions to none.
    protected function handleGroupPermissions($object, $fields, $key, $value)
    {
        $group = app()->make(GroupRepository::class)->getById(explode('_', $key)[0]);

        // The value has changed
        if ($this->allUsersInGroupAuthorized($group, $object) !== $value) {
            foreach ($group->users as $user) {
                // Group checked, grant at least view access to all users in the group.
                if ($value && !$user->itemPermissionName($object)) {
                    $permission = new Permission;
                    $permission->name = "view";
                    $permission->permissionable()->associate($object);
                    $user->permissions()->save($permission);
                }
                // Group unchecked, revoke all users who have view permissions to none.
                elseif (!$value && $user->itemPermissionName($object) === "view") {
                    $user->itemPermission($object)->delete();
                }
            }
        }
    }

    protected function allUsersInGroupAuthorized($group, $object)
    {
        return $group->users()->whereDoesntHave('permissions', function ($query) use ($object) {
            $query->where([
                ['permissionable_type', get_class($object)],
                ['permissionable_id', $object->id],
            ]);
        })->get()->count() === 0;
    }

}
