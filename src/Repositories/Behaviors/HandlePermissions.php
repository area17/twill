<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Enums\PermissionLevel;
use A17\Twill\Facades\TwillPermissions;
use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use A17\Twill\Models\Permission;

trait HandlePermissions
{
    /**
     * Retrieve user-item permissions fields
     *
     * @param TwillModelContract $object
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
        foreach ($userGroups as $group) {
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
            ->mapWithKeys(function ($scope) {
                return [$scope => 0];
            })
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
     * @param array $fields
     */
    public function afterSaveHandlePermissions($object, $fields)
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

    private function shouldProcessPermissions($moduleName): bool
    {
        return TwillPermissions::levelIs(PermissionLevel::LEVEL_ROLE_GROUP_ITEM)
            && TwillPermissions::getPermissionModule($moduleName);
    }

    private function storePermissionFields($moduleName, $object, $permissionFields)
    {
        Session::put("{$moduleName}_{$object->id}_item_permissions", $permissionFields);
    }

    private function recallPermissionFields($moduleName, $object)
    {
        return Session::get("{$moduleName}_{$object->id}_item_permissions") ?: [];
    }
}
