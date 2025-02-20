<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\Role;
use Illuminate\Support\Str;
use A17\Twill\Models\Permission;

trait HandleRolePermissions
{
    /**
     * Retrieve role permissions fields
     *
     * @param TwillModelContract|Role $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleRolePermissions($object, $fields)
    {
        $object->permissions()->get();

        foreach ($object->permissions()->global()->pluck('name')->toArray() as $permissionName) {
            $fields[$permissionName] = true;
        }

        foreach (Permission::permissionableModules() as $moduleName) {
            $modulePermission = $object->permissions()->module()->ofModuleName($moduleName)->first();
            if ($modulePermission) {
                $fields['module_' . $moduleName . '_permissions'] = $modulePermission->name;
            } else {
                $fields['module_' . $moduleName . '_permissions'] = 'none';
            }
        }

        return $fields;
    }

    /**
     * Function executed after save on role form
     *
     * @param TwillModelContract|Role $object
     * @param array $fields
     */
    public function afterSaveHandleRolePermissions($object, $fields)
    {
        $this->addOrRemoveUsersToEveryoneGroup($object);

        $this->updateRolePermissions($object, $fields);
    }

    private function addOrRemoveUsersToEveryoneGroup($role)
    {
        $everyoneGroup = twillModel('group')::getEveryoneGroup();
        $roleUserIds = $role->users->pluck('id')->toArray();

        if ($role->in_everyone_group) {
            $everyoneGroup->users()->syncWithoutDetaching($roleUserIds);
        } else {
            $everyoneGroup->users()->detach($roleUserIds);
        }
    }

    private function updateRolePermissions($role, $fields)
    {
        foreach (Permission::available(Permission::SCOPE_GLOBAL) as $permissionName) {
            if (isset($fields[$permissionName]) && $fields[$permissionName] === true) {
                $role->grantGlobalPermission($permissionName);
            } else {
                $role->revokeGlobalPermission($permissionName);
            }
        }

        foreach ($fields as $key => $permissionName) {
            if (Str::startsWith($key, 'module_') && Str::endsWith($key, '_permissions')) {
                $modulePermissions = Permission::available(Permission::SCOPE_MODULE);
                $model = getModelByModuleName($moduleName = explode('_', $key)[1]);

                $currentPermission = $role->permissions()
                    ->where('permissionable_type', $model)
                    ->whereIn('name', $modulePermissions)
                    ->first();

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
}
