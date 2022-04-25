<?php

namespace A17\Twill\Repositories\Behaviors;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use A17\Twill\Models\Permission;

trait HandlePermissions
{
    /**
     * Retrieve user-item permissions fields
     *
     * @param Model $object
     * @return array
     * @param mixed[] $fields
     */
    public function getFormFieldsHandlePermissions($object, array $fields)
    {
        $moduleName = getModuleNameByModel($object);

        if (!$this->shouldProcessPermissions($moduleName)) {
            return $fields;
        }

        $userItemPermissions = twillModel('user')::notSuperAdmin()->get()->mapWithKeys(
            function ($user) use ($object, $moduleName): array {
                $permissionName = $this->getUserItemPermissionName($user, $object, $moduleName);

                return [sprintf('user_%s_permission', $user->id) => $permissionName];
            }
        )->toArray();

        $this->storePermissionFields($moduleName, $object, $userItemPermissions);

        return $fields + $userItemPermissions;
    }

    private function getUserItemPermissionName($user, $item, $moduleName)
    {
        if ($user->role->permissions()->global()->where('name', 'manage-modules')->first()) {
            return 'manage-item';
        }

        $allPermissionNames = collect([]);

        // Role-Module permission
        if ($modulePermission = $user->role->permissions()->ofModuleName($moduleName)->first()) {
            $allPermissionNames->push(str_replace('-module', '-item', $modulePermission->name));
        }

        // Group-Item permissions
        $userGroups = $user->groups()->where('is_everyone_group', false)->get();
        foreach($userGroups as $group) {
            if ($permission = $group->permissions()->ofItem($item)->first()) {
                $allPermissionNames->push($permission->name);
            }
        }

        // User-Item permission
        if ($itemPermission = $user->permissions()->ofItem($item)->first()) {
            $allPermissionNames->push($itemPermission->name);
        }

        return $this->getHighestItemPermissionName($allPermissionNames->filter());
    }

    private function getHighestItemPermissionName($permissionNames)
    {
        if (count($permissionNames) <= 1) {
            return $permissionNames[0] ?? '';
        }

        $itemScopes = collect(Permission::available(Permission::SCOPE_ITEM))
            ->reverse()
            ->mapWithKeys(function ($scope): array { return [$scope => 0]; })
            ->toArray();

        foreach ($permissionNames as $name) {
            if (isset($itemScopes[$name])) {
                ++$itemScopes[$name];
            }
        }

        foreach ($itemScopes as $scope => $count) {
            if ($count > 0) {
                return $scope;
            }
        }

        return '';
    }

    /**
     * Function executed after save on module form
     *
     * @param Model $object
     * @param mixed[] $fields
     */
    public function afterSaveHandlePermissions($object, array $fields)
    {
        $moduleName = getModuleNameByModel($object);

        if (!$this->shouldProcessPermissions($moduleName)) {
            return;
        }

        $oldItemPermissions = $this->recallPermissionFields($moduleName, $object);

        foreach ($fields as $key => $value) {
            if (!Str::endsWith($key, '_permission')) {
                continue;
            }

            if (isset($oldItemPermissions[$key]) && $oldItemPermissions[$key] === $value) {
                continue;
            }

            $userId = explode('_', $key)[1];
            $user = twillModel('user')::find($userId);

            if ($value) {
                $user->grantModuleItemPermission($value, $object);
            } else {
                $user->revokeModuleItemAllPermissions($object);
            }
        }
    }

    private function shouldProcessPermissions($moduleName)
    {
        return config('twill.enabled.permissions-management')
            && config('twill.permissions.level') === 'roleGroupItem'
            && isPermissionableModule($moduleName);
    }

    private function storePermissionFields($moduleName, $object, $permissionFields)
    {
        Session::put(sprintf('%s_%s_item_permissions', $moduleName, $object->id), $permissionFields);
    }

    private function recallPermissionFields($moduleName, $object)
    {
        return Session::get(sprintf('%s_%s_item_permissions', $moduleName, $object->id)) ?: [];
    }
}
