<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Permission;
use A17\Twill\Models\Group;
use A17\Twill\Repositories\UserRepository;
use A17\Twill\Repositories\GroupRepository;
use DB;

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
        elseif (isPermissionableModule(getModuleNameByModel(get_class($object)))) {
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
        elseif (isPermissionableModule(getModuleNameByModel(get_class($object)))) {
            $this->handleModulePermissions($object, $fields);
        }
    }

    // After save handle permissions form fields on user form
    protected function handleUserPermissions($user, $fields)
    {
        $changes = $user->getChanges();
        if (isset($changes['role_id'])) {
            $this->revokeAllPermissions($user);
            # update the permission of the user
            foreach($user->role->permissions()->module()->get() as $permission) {
                $permissionName = str_replace("-module","-item", $permission->name);
                $repository = getModelRepository(getModuleNameByModel($permission->permissionable_type));
                foreach($repository->get() as $item) {
                    $user->grantModuleItemPermission($permissionName, $item);
                }
            }
        } else {
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
    }

    public function revokeAllPermissions($user)
    {
        $permissions = $user->permissions->pluck('id')->all();
        if( !empty($permissions)) {
            DB::table('permissions')->whereIn('id',$permissions)->delete();
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
                    ->where('permissionable_type',$model)
                    ->whereIn('name', $modulePermissions)
                    ->first()
                ;

                if (!$currentPermission || $permissionName != $currentPermission->name) {
                    $role->revokeAllModulePermission($model);
                    if (in_array($permissionName, $modulePermissions)) {
                        $role->grantModulePermission($permissionName, $model);
                    }
                    //Update user's permissions
                    $users = $role->users;
                    foreach($users as $user) {
                        $this->revokeAllPermissions($user);
                    }

                    if ($permissionName != 'none') {
                        $repository = getRepositoryByModuleName($moduleName);
                        $permissionName = str_replace("-module","-item", $permissionName);

                        foreach($repository->get() as $item) {
                            foreach($users as $user) {
                                $user->grantModuleItemPermission($permissionName, $item);
                            }
                        }
                    }
                }
            }
        }

        $inEveryoneGroup = isset($fields['groups']) && in_array('include-in-everyone', $fields['groups']);

        if ($inEveryoneGroup) {
            Group::getEveryoneGroup()->users()->syncWithoutDetaching($role->users()->pluck('id'));
        } else {
            Group::getEveryoneGroup()->users()->detach($role->users()->pluck('id'));
        }

        $role->in_everyone_group = $inEveryoneGroup;

        $role->save();
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
            //Handle group permissions
            elseif (ends_with($key, '_group_authorized')) {
                $group_id = explode('_', $key)[0];
                $group = app()->make(GroupRepository::class)->getById($group_id);

                if ($value) {
                    $group->grantModuleItemPermission('view-item', $item);
                } else {
                    $group->revokeModuleItemAllPermissions($item);
                }
            }
        }
    }

    protected function handleGroupPermissions($object, $fields)
    {

    }

    protected function renderUserPermissions($user, $fields)
    {
        if ($user->role) {
            foreach ($user->permissions()->moduleItem()->get() as $permission) {
                $model = $permission->permissionable()->first();
                $moduleName = getModuleNameByModel($model);
                $fields[$moduleName . '_' . $model->id . '_permission'] = '"' . $permission->name . '"';
            }

            #If the user has the 'manage-modules' permission
            $isManageAllModules = ($user->role->permissions()->global()->where('name','manage-modules')->first() != null);

            #looking for module permission
            $globalPermissions = [];
            if (!$isManageAllModules) {
                foreach($user->role->permissions()->module()->get() as $permission) {
                    if ($permission->permissionable_type) {
                        $permissionName = str_replace("-module","-item", $permission->name);

                        $globalPermissions[getModuleNameByModel($permission->permissionable_type)] = $permissionName;
                    }
                }
            }

            #looking for item permission
            $scopes = Permission::available('item');
            foreach(Permission::permissionableParentModuleItems() as $moduleName => $moduleItems) {
                if (isset($globalPermissions[$moduleName]) || $isManageAllModules) {
                    $permission = $isManageAllModules ? 'manage-item' : $globalPermissions[$moduleName];

                    foreach ($moduleItems as $moduleItem) {
                        $index = $moduleName . '_' . $moduleItem->id . '_permission';
                        if( !isset($fields[$index])) {
                            $fields[$index] = "\"{$permission}\"";
                        } else {
                            $current = array_search(str_replace('"',"",$fields[$index]), $scopes);
                            $global = array_search($permission, $scopes);
                            #check permission level
                            if ($global > $current) {
                                $fields[$index] = "\"{$permission}\"";
                            }
                        }
                    }
                }
            }
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
            $defaultPermission = "";
            $permission = $user->permissions()->moduleItem()->ofItem($object)->first();
            if (!$permission && $user->role->permissions()->global()->where('name', 'manage-modules')->first()) {
                $defaultPermission = "'manage-item'";
            }

            if(empty($defaultPermission)) {
                foreach($user->role->permissions()->module()->get() as $p) {
                    if ($p->permissionable_type==get_class($object) && $p->permissionable_id==null) {
                        $defaultPermission = "'".str_replace("-module", "-item", $p->name)."'";
                        break;
                    }
                }
            }

            $fields['user_' . $user->id . '_permission'] = $permission ? "'" . $permission->name . "'" : $defaultPermission;
        }

        // Render each group's permission under a item
        $groups = Group::with('users.permissions')->get();
        foreach ($groups as $group) {
            $fields[$group->id . '_group_authorized'] = $this->allUsersInGroupAuthorized($group, $object);
        }

        return $fields;
    }

    protected function renderRolePermissions($role, $fields)
    {
        $role->permissions()->get();

        foreach($role->permissions()->global()->pluck('name')->toArray() as $permissionName) {
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

        $fields['groups'] = $role->in_everyone_group ? ['include-in-everyone'] : [];
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

}
