<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\User;
use A17\Twill\Models\Model;
use Illuminate\Support\Str;
use A17\Twill\Models\Permission;

trait HandleUserPermissions
{
    /**
     * Retrieve user permissions fields
     *
     * @param Model|User $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleUserPermissions($object, $fields)
    {
        if (!config('twill.enabled.permissions-management')) {
            return $fields;
        }

        foreach ($object->permissions()->moduleItem()->get() as $permission) {
            $model = $permission->permissionable()->first();
            $moduleName = getModuleNameByModel($model);
            $fields[$moduleName . '_' . $model->id . '_permission'] = $permission->name;
        }

        \Session::put("user-{$object->id}", $fields = $this->getUserPermissionsFields($object, $fields));

        return $fields;
    }

    /**
     * Function executed after save on user form
     *
     * @param Model|User $object
     * @param array $fields
     */
    public function afterSaveHandleUserPermissions($object, $fields)
    {
        if (!config('twill.enabled.permissions-management')) {
            return;
        }

        $oldFields = \Session::get("user-{$object->id}");

        foreach ($fields as $key => $value) {
            if (Str::endsWith($key, '_permission')) {
                // Old permission
                if (isset($oldFields[$key]) && $oldFields[$key] == $value) {
                    continue;
                }

                $item_name = explode('_', $key)[0];
                $item_id = explode('_', $key)[1];
                $item = getRepositoryByModuleName($item_name)->getById($item_id);

                // Only value existed, do update or create
                if ($value) {
                    $object->grantModuleItemPermission($value, $item);
                } else {
                    $object->revokeModuleItemAllPermissions($item);
                }
            }
        }
    }

    /**
     * Get user permissions fields
     *
     * @param Model|User $user
     * @param array $fields
     * @return void
     */
    protected function getUserPermissionsFields($user, $fields)
    {
        if (!config('twill.enabled.permissions-management')) {
            return $fields;
        }

        $itemScopes = Permission::available(Permission::SCOPE_ITEM);

        // looking for group permissions that belongs to the user
        foreach ($user->publishedGroups as $group) {

            // get each permissions that belongs to a module from this group
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

                    // check that group permission is greater that current permission level
                    if ($group > $current) {
                        $fields[$index] = $permission->name;
                    }
                } else {
                    $fields[$index] = $permission->name;
                }
            }
        }

        // looking for global permissions, if the user has the 'manage-modules' permission
        $isManageAllModules = $user->isSuperAdmin() || ($user->role->permissions()->global()->where('name', 'manage-modules')->first() != null);

        // looking for role module permission
        $globalPermissions = [];
        if (!$isManageAllModules) {
            foreach ($user->role->permissions()->module()->get() as $permission) {
                if ($permission->permissionable_type) {
                    $permissionName = str_replace("-module", "-item", $permission->name);
                    $globalPermissions[getModuleNameByModel($permission->permissionable_type)] = $permissionName;
                }
            }
        }

        // merge all permissions
        // go through all existing modules
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

                        // check permission level
                        if ($global > $current) {
                            $fields[$index] = "\"{$permission}\"";
                        }
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * Retrieve count of user for 'activated' and 'pending' status slug
     *
     * @param string $slug
     * @param array $scope
     * @return int|boolean
     */
    public function getCountByStatusSlugHandleUserPermissions($slug, $scope = [])
    {
        $query = $this->model->where($scope);

        if (get_class($this->model) === twillModel('user')) {
            if ($slug === 'activated') {
                return $query->notSuperAdmin()->activated()->count();
            }

            if ($slug === 'pending') {
                return $query->notSuperAdmin()->pending()->count();
            }
        }

        return false;
    }
}
