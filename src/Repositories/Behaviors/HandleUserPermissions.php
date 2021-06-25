<?php

namespace A17\Twill\Repositories\Behaviors;

use Illuminate\Support\Str;
use A17\Twill\Models\Permission;

trait HandleUserPermissions
{
    public function getFormFieldsHandleUserPermissions($object, $fields)
    {
        $fields = $this->renderUserPermissions($object, $fields);

        return $fields;
    }

    public function afterSaveHandleUserPermissions($object, $fields)
    {
        $this->handleUserPermissions($object, $fields);
    }

    // After save handle permissions form fields on user form
    protected function handleUserPermissions($user, $fields)
    {
        $oldFields = \Session::get("user-{$user->id}");
        foreach ($fields as $key => $value) {
            if (Str::endsWith($key, '_permission')) {
                //Old permission
                if (isset($oldFields[$key]) && $oldFields[$key] == $value) {
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

    protected function renderUserPermissions($user, $fields)
    {
        #looking for user permissions
        foreach ($user->permissions()->moduleItem()->get() as $permission) {
            $model = $permission->permissionable()->first();
            $moduleName = getModuleNameByModel($model);
            $fields[$moduleName . '_' . $model->id . '_permission'] = $permission->name;
        }

        \Session::put("user-{$user->id}", $fields = $this->getUserPermissionsFields($user, $fields));
        return $fields;
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
                    $current = array_search($fields[$index], $itemScopes);
                    $group = array_search($permission->name, $itemScopes);
                    #check permission level
                    if ($group > $current) {
                        $fields[$index] = "\"{$permission->name}\"";
                    }
                } else {
                    $fields[$index] = $permission->name;
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
                        $current = array_search($fields[$index], $itemScopes);
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

    public function getCountByStatusSlugHandleUserPermissions($slug, $scope = [])
    {
        $query = $this->model->where($scope);

        if ($this->model::class === twillModel('user')) {
            if ($slug === 'activated') {
                return $query->activated()->count();
            }

            if ($slug === 'pending') {
                return $query->pending()->count();
            }
        }

        return false;
    }
}
