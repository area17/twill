<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Role;
use Illuminate\Support\Str;
use A17\Twill\Models\Permission;

trait HandleRolePermissions
{
    /**
     * Retrieve role permissions fields
     *
     * @param Model|Role $object
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
     * @param Model|Role $object
     * @param array $fields
     */
    public function afterSaveHandleRolePermissions($object, $fields)
    {
        foreach (Permission::available(Permission::SCOPE_GLOBAL) as $permissionName) {
            if (isset($fields[$permissionName]) && $fields[$permissionName] === true) {
                $object->grantGlobalPermission($permissionName);
            } else {
                $object->revokeGlobalPermission($permissionName);
            }
        }

        foreach ($fields as $key => $permissionName) {
            if (Str::startsWith($key, 'module_') && Str::endsWith($key, '_permissions')) {
                $modulePermissions = Permission::available(Permission::SCOPE_MODULE);
                $model = getModelByModuleName($moduleName = explode('_', $key)[1]);

                $currentPermission = $object->permissions()
                    ->where('permissionable_type', $model)
                    ->whereIn('name', $modulePermissions)
                    ->first()
                ;

                if (!$currentPermission || $permissionName != $currentPermission->name) {
                    $object->revokeAllModulePermission($model);
                    if (in_array($permissionName, $modulePermissions)) {
                        $object->grantModulePermission($permissionName, $model);
                    }
                }
            }
        }

        $object->save();
    }
}
