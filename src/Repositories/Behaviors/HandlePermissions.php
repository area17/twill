<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Permission;

trait HandlePermissions
{
    public function getFormFieldsHandlePermissions($object, $fields)
    {
        //User form page
        if (get_class($object) === twillModel('user')) {
            $fields = $this->renderUserPermissions($object, $fields);
        }
        // Group form page
        elseif (get_class($object) === twillModel('group')) {
            $fields = $this->renderGroupPermissions($object, $fields);
        }
        // Role form page
        elseif (get_class($object) === twillModel('role')) {
            $fields = $this->renderRolePermissions($object, $fields);
        }
        // Module item form page
        elseif (isPermissionableModule(getModuleNameByModel(get_class($object)))) {
            $fields = $this->renderModulePermissions($object, $fields);
        }

        return $fields;
    }

    public function afterSaveHandlePermissions($object, $fields)
    {
        // User form page
        if (get_class($object) === twillModel('user')) {
            $this->handleUserPermissions($object, $fields);
        }
        // Group form page
        elseif (get_class($object) === twillModel('group')) {
            $this->handleGroupPermissions($object, $fields);
        }
        // Role form page
        elseif (get_class($object) === twillModel('role')) {
            $this->handleRolePermissions($object, $fields);
        }
        // Module item form page
        elseif (isPermissionableModule(getModuleNameByModel(get_class($object)))) {
            $this->handleModulePermissions($object, $fields);
        }
    }

    // After save handle permissions form fields on user form
    protected function handleUserPermissions($user, $fields)
    {
        $oldFields = \Session::get("user-{$user->id}");
        foreach ($fields as $key => $value) {
            if (ends_with($key, '_permission')) {
                //Old permission
                if (isset($oldFields[$key]) && $oldFields[$key] == '"' . $value . '"') {
                    continue;
                }
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
            if (isset($fields[$permissionName]) && $fields[$permissionName] === true) {
                $role->grantGlobalPermission($permissionName);
            } else {
                $role->revokeGlobalPermission($permissionName);
            }
        }

        foreach ($fields as $key => $permissionName) {
            if (starts_with($key, 'module_') && ends_with($key, '_permissions')) {
                $modulePermissions = Permission::available('module');
                $model = getModelByModuleName($moduleName = explode('_', $key)[1]);

                $currentPermission = $role->permissions()
                    ->where('permissionable_type', $model)
                    ->whereIn('name', $modulePermissions)
                    ->first()
                ;

                if (!$currentPermission || $permissionName != $currentPermission->name) {
                    $role->revokeAllModulePermission($model);
                    if (in_array($permissionName, $modulePermissions)) {
                        $role->grantModulePermission($permissionName, $model);
                    }
                }
            }
        }

        $role->save();
    }

    // After save handle permissions form fields on module form
    protected function handleModulePermissions($item, $fields)
    {
        //...
    }

    protected function handleGroupPermissions($group, $fields)
    {
        foreach (Permission::available('global') as $permissionName) {
            if (isset($fields[$permissionName]) && $fields[$permissionName] === true) {
                $group->grantGlobalPermission($permissionName);
            } else {
                $group->revokeGlobalPermission($permissionName);
            }
        }

        foreach ($fields as $key => $permissionName) {
            //Used for the roleGroup mode
            if (starts_with($key, 'module_') && ends_with($key, '_permissions')) {
                $modulePermissions = Permission::available('module');
                $model = getModelByModuleName($moduleName = explode('_', $key)[1]);

                $currentPermission = $group->permissions()
                    ->where('permissionable_type', $model)
                    ->whereIn('name', $modulePermissions)
                    ->first()
                ;

                if (!$currentPermission || $permissionName != $currentPermission->name) {
                    $group->revokeAllModulePermission($model);
                    if (in_array($permissionName, $modulePermissions)) {
                        $group->grantModulePermission($permissionName, $model);
                    }
                }
            } elseif (ends_with($key, '_permission')) {
                $item_name = explode('_', $key)[0];
                $item_id = explode('_', $key)[1];
                $item = getRepositoryByModuleName($item_name)->getById($item_id);

                // Only permissionName existed, do update or create
                if ($permissionName) {
                    $group->grantModuleItemPermission($permissionName, $item);
                } else {
                    $group->revokeModuleItemAllPermissions($item);
                }
            }
        }
    }

    protected function renderUserPermissions($user, $fields)
    {
        #looking for user permissions
        foreach ($user->permissions()->moduleItem()->get() as $permission) {
            $model = $permission->permissionable()->first();
            $moduleName = getModuleNameByModel($model);
            $fields[$moduleName . '_' . $model->id . '_permission'] = '"' . $permission->name . '"';
        }

        \Session::put("user-{$user->id}", $fields = $this->getUserPermissionsFields($user, $fields));
        return $fields;
    }

    protected function renderGroupPermissions($group, $fields)
    {
        $fields = [];
        if (\Config::get('twill.permission.level') == 'roleGroup') {
            foreach (Permission::permissionableModules() as $moduleName) {
                $modulePermission = $group->permissions()->module()->ofModuleName($moduleName)->first();
                if ($modulePermission) {
                    $fields['module_' . $moduleName . '_permissions'] = '"' . $modulePermission->name . '"';
                } else {
                    $fields['module_' . $moduleName . '_permissions'] = '"none"';
                }
            }
        } elseif (\Config::get('twill.permission.level') == 'roleGroupModule') {
            #looking for item permissions
            foreach ($group->permissions()->moduleItem()->get() as $permission) {
                $model = $permission->permissionable()->first();
                $moduleName = getModuleNameByModel($model);
                $fields[$moduleName . '_' . $model->id . '_permission'] = '"' . $permission->name . '"';
            }
        }

        return $fields;
    }

    protected function renderModulePermissions($object, $fields)
    {
        //...

        return $fields;
    }

    protected function renderRolePermissions($role, $fields)
    {
        $role->permissions()->get();

        foreach ($role->permissions()->global()->pluck('name')->toArray() as $permissionName) {
            $fields[$permissionName] = true;
        }

        foreach (Permission::permissionableModules() as $moduleName) {
            $modulePermission = $role->permissions()->module()->ofModuleName($moduleName)->first();
            if ($modulePermission) {
                $fields['module_' . $moduleName . '_permissions'] = '"' . $modulePermission->name . '"';
            } else {
                $fields['module_' . $moduleName . '_permissions'] = '"none"';
            }
        }

        return $fields;
    }

    protected function allUsersInGroupAuthorized($group, $object)
    {
        return $group->users()->count() > 0 && $group->users()->whereDoesntHave('permissions', function ($query) use ($object) {
            $query->where([
                ['permissionable_type', get_class($object)],
                ['permissionable_id', $object->id],
            ]);
        })->get()->count() === 0;
    }

    public function isPublicItemExists()
    {
        if ($this->model->isFillable('public')) {
            return $this->model->publishedInListings()->exists();
        }
        return false;
    }

    protected function getUserPermissionsFields($user, $fields)
    {
        $itemScopes = Permission::available('item');

        #looking for group permissions belongs to the user
        foreach ($user->publishedGroups as $group) {
            foreach ($group->permissions()->moduleItem()->get() as $permission) {
                $model = $permission->permissionable()->first();

                if (!$model) {
                    continue;
                }

                $moduleName = getModuleNameByModel($model);

                $index = $moduleName . '_' . $model->id . '_permission';
                if (isset($fields[$index])) {
                    $current = array_search(str_replace('"', "", $fields[$index]), $itemScopes);
                    $group = array_search($permission->name, $itemScopes);
                    #check permission level
                    if ($group > $current) {
                        $fields[$index] = "\"{$permission->name}\"";
                    }
                } else {
                    $fields[$index] = '"' . $permission->name . '"';
                }
            }
        }

        #looking for global permissions, if the user has the 'manage-modules' permission
        $isManageAllModules = $user->is_superadmin || ($user->role->permissions()->global()->where('name', 'manage-modules')->first() != null);

        #looking for role module permission
        $globalPermissions = [];
        if (!$isManageAllModules) {
            foreach ($user->role->permissions()->module()->get() as $permission) {
                if ($permission->permissionable_type) {
                    $permissionName = str_replace("-module", "-item", $permission->name);

                    $globalPermissions[getModuleNameByModel($permission->permissionable_type)] = $permissionName;
                }
            }
        }

        #merge all permissions
        #go through all existing modules
        foreach (Permission::permissionableParentModuleItems() as $moduleName => $moduleItems) {
            if (isset($globalPermissions[$moduleName]) || $isManageAllModules) {
                $permission = $isManageAllModules ? 'manage-item' : $globalPermissions[$moduleName];

                foreach ($moduleItems as $moduleItem) {
                    $index = $moduleName . '_' . $moduleItem->id . '_permission';
                    if (!isset($fields[$index])) {
                        $fields[$index] = "\"{$permission}\"";
                    } else {
                        $current = array_search(str_replace('"', "", $fields[$index]), $itemScopes);
                        $global = array_search($permission, $itemScopes);
                        #check permission level
                        if ($global > $current) {
                            $fields[$index] = "\"{$permission}\"";
                        }
                    }
                }
            }
        }

        return $fields;
    }
}
