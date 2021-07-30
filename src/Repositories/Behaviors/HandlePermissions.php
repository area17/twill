<?php

namespace A17\Twill\Repositories\Behaviors;

use Illuminate\Support\Str;
use A17\Twill\Models\Permission;

trait HandlePermissions
{
    /**
     * Retrieve user-item permissions fields
     *
     * @param Model|User $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandlePermissions($object, $fields)
    {
        $moduleName = getModuleNameByModel($object);

        if (!$this->shouldProcessPermissions($moduleName)) {
            return $fields;
        }

        $userItemPermissions = twillModel('user')::notSuperAdmin()->get()->mapWithKeys(
            function ($user) use ($object, $moduleName) {
                $permissionName = $this->getUserItemPermissionName($user, $object, $moduleName);

                return ["user_{$user->id}_permission" => $permissionName];
            }
        )->toArray();

        return $fields + $userItemPermissions;
    }

    private function getUserItemPermissionName($user, $item, $moduleName)
    {
        if ($user->role->permissions()->global()->where('name', 'manage-modules')->first()) {
            return 'manage-item';
        }

        $allPermissionNames = collect([]);

        // Role-module permission
        if ($modulePermission = $user->role->permissions()->ofModuleName($moduleName)->first()) {
            $allPermissionNames->push(str_replace('-module', '-item', $modulePermission->name));
        }

        // Group-module permissions
        $userGroups = $user->groups()->where('is_everyone_group', false)->get();
        if ($userGroups->count() > 0) {
            $groupPermissionNames = $userGroups->map(function ($group) use ($moduleName) {
                return $group->permissions()->ofModuleName($moduleName)->first();
            })->filter()->pluck('name')->toArray();

            $allPermissionNames->concat($groupPermissionNames);
        }

        // Item permission
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
            ->mapWithKeys(function ($scope) { return [$scope => 0]; })
            ->toArray();

        foreach ($permissionNames as $name) {
            if (isset($itemScopes[$name])) {
                $itemScopes[$name]++;
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
     * @param array $fields
     */
    public function afterSaveHandlePermissions($object, $fields)
    {
        $moduleName = getModuleNameByModel($object);

        if (!$this->shouldProcessPermissions($moduleName)) {
            return;
        }

        // ...
    }

    private function shouldProcessPermissions($moduleName)
    {
        return config('twill.enabled.permissions-management')
            && config('twill.permissions.level') === 'roleGroupModule'
            && isPermissionableModule($moduleName);
    }
}
