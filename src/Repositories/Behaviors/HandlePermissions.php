<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Permission;
use A17\Twill\Repositories\GroupRepository;
use A17\Twill\Repositories\UserRepository;

trait HandlePermissions
{

    public function getFormFieldsHandlePermissions($object, $fields)
    {
        //User form page
        if (get_class($object) === "A17\Twill\Models\User") {
            $fields = $this->renderUserPermissions($object, $fields);
        }
        // Group form page
        elseif (get_class($object) === "A17\Twill\Models\Group") {
            $fields = $this->renderGroupPermissions($object, $fields);
        }
        // Role form page
        elseif (get_class($object) === "A17\Twill\Models\Role") {
            $fields = $this->renderRolePermissions($object, $fields);
        }
        // Module item form page
        elseif (Permission::permissionableModules()->contains(class_basename($object))) {
            $fields = $this->renderModulePermissions($object, $fields);
        }

        return $fields;
    }

    public function afterSaveHandlePermissions($object, $fields)
    {
        // User form page
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
        elseif (Permission::permissionableModules()->contains(class_basename($object))) {
            $this->handleModulePermissions($object, $fields);
        }
    }

    // After save handle permissions form fields on user form
    protected function handleUserPermissions($user, $fields)
    {
        foreach ($fields as $key => $value) {
            if (ends_with($key, '_permission')) {
                $item_name = explode('_', $key)[0];
                $item_id = explode('_', $key)[1];
                $item = getRepositoryByModuleName($item_name)->getById($item_id);

                // Only value existed, do update or create
                if ($value) {
                    $user->grantModuleItemPermission($value, $item);
                } else {
                    $user->revokeModuleItemAllPermissions($item);
                }
            }
        }
    }

    // After save handle permissions form fields on role form
    protected function handleRolePermissions($role, $fields)
    {
        foreach (Permission::available('global') as $permissionName) {
            if (isset($fields['general-permissions']) && in_array($permissionName, $fields['general-permissions'])) {
                $role->grantGlobalPermission($permissionName);
            } else {
                $role->revokeGlobalPermission($permissionName);
            }
        }
    }

    // After save handle permissions form fields on module form
    protected function handleModulePermissions($item, $fields)
    {
        foreach ($fields as $key => $value) {
            if (ends_with($key, '_permission') && starts_with($key, 'user')) {
                $user_id = explode('_', $key)[1];
                $user = app()->make(UserRepository::class)->getById($user_id);

                // Only if value existed, do update or create
                if ($value) {
                    $user->grantModuleItemPermission($value, $item);
                } else {
                    $user->revokeModuleItemAllPermissions($item);
                }
            }
            // Handle group permissions
            elseif (ends_with($key, '_group_authorized')) {
                $group = app()->make(GroupRepository::class)->getById(explode('_', $key)[0]);
            }
        }
    }

    protected function handleGroupPermissions($object, $fields)
    {

    }

    // After save handle permissions form fields on group form:
    // If one group checked, switch the permission of all users in the group to at least view.
    // If one group unchecked, switch the permission of all users who have view permissions to none.
    // protected function handleGroupPermissions($object, $fields, $key, $value)
    // {
    //     $group = app()->make(GroupRepository::class)->getById(explode('_', $key)[0]);

    //     // The value has changed
    //     if ($this->allUsersInGroupAuthorized($group, $object) !== $value) {
    //         foreach ($group->users as $user) {
    //             // Group checked, grant at least view access to all users in the group.
    //             if ($value && !$user->permissionNameByItem($object)) {
    //                 $permission = new Permission;
    //                 $permission->name = "view";
    //                 $permission->permissionable()->associate($object);
    //                 $user->permissions()->save($permission);
    //             }
    //             // Group unchecked, revoke all users who have view permissions to none.
    //             elseif (!$value && $user->permissionNameByItem($object) === "view") {
    //                 $user->permissionsByItem($object)->delete();
    //             }
    //         }
    //     }
    // }

    protected function renderUserPermissions($user, $fields)
    {
        foreach ($user->itemPermissions as $permission) {
            $module = $permission->permissionable()->first();
            $module_name = str_plural(lcfirst(class_basename($module)));
            $fields[$module_name . '_' . $module->id . '_permission'] = '"' . $permission->name . '"';
        }
        return $fields;
    }

    protected function renderGroupPermissions($object, $fields)
    {
        // Nothing to do for now
    }

    protected function renderModulePermissions($object, $fields)
    {
        // Render each user's permission under a item
        $users = app()->make(UserRepository::class)->get(["permissions" => function ($query) use ($object) {
            $query->where([['permissionable_type', get_class($object)], ['permissionable_id', $object->id]]);
        }]);

        foreach ($users as $user) {
            $permission = $user->permissions->first();
            $fields['user_' . $user->id . '_permission'] = $permission ? "'" . $permission->name . "'" : "";
        }

        // Render each group's permission under a item
        $groups = app()->make(GroupRepository::class)->get(['users.permissions']);
        foreach ($groups as $group) {
            $fields[$group->id . '_group_authorized'] = $this->allUsersInGroupAuthorized($group, $object);
        }

        return $fields;
    }

    protected function renderRolePermissions($object, $fields)
    {
        $object->permissions()->get();
        $fields["general-permissions"] = $object->permissions()->where([
            "permissionable_type" => null,
            "permissionable_id" => null,
        ])->pluck("name")->toArray();
        return $fields;
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
