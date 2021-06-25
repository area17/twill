<?php

namespace A17\Twill\Repositories\Behaviors;

use Illuminate\Support\Str;
use A17\Twill\Models\Permission;

trait HandleRolePermissions
{
    public function getFormFieldsHandleRolePermissions($object, $fields)
    {
        $fields = $this->renderRolePermissions($object, $fields);

        return $fields;
    }

    public function afterSaveHandleRolePermissions($object, $fields)
    {
        $this->handleRolePermissions($object, $fields);
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
            if (Str::startsWith($key, 'module_') && Str::endsWith($key, '_permissions')) {
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

    protected function renderRolePermissions($role, $fields)
    {
        $role->permissions()->get();

        foreach ($role->permissions()->global()->pluck('name')->toArray() as $permissionName) {
            $fields[$permissionName] = true;
        }

        foreach (Permission::permissionableModules() as $moduleName) {
            $modulePermission = $role->permissions()->module()->ofModuleName($moduleName)->first();
            if ($modulePermission) {
                $fields['module_' . $moduleName . '_permissions'] = $modulePermission->name;
            } else {
                $fields['module_' . $moduleName . '_permissions'] = 'none';
            }
        }

        return $fields;
    }
}
