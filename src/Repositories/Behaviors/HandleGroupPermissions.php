<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Enums\PermissionLevel;
use A17\Twill\Facades\TwillPermissions;
use A17\Twill\Models\Group;
use A17\Twill\Models\Model;
use Illuminate\Support\Str;
use A17\Twill\Models\Permission;

trait HandleGroupPermissions
{
    /**
     * Retrieve group permissions fields
     *
     * @param Model|Group $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleGroupPermissions($object, $fields)
    {
        if (TwillPermissions::levelIs(PermissionLevel::LEVEL_ROLE_GROUP)) {
            // Add active global permissions
            foreach ($object->permissions()->global()->pluck('name') as $permissionName) {
                $fields[$permissionName] = true;
            }

            // Add active module permissions
            foreach (Permission::permissionableModules() as $moduleName) {
                $modulePermission = $object->permissions()->module()->ofModuleName($moduleName)->first();
                if ($modulePermission) {
                    $fields['module_' . $moduleName . '_permissions'] = $modulePermission->name;
                } else {
                    $fields['module_' . $moduleName . '_permissions'] = 'none';
                }
            }
        } elseif (TwillPermissions::levelIs(PermissionLevel::LEVEL_ROLE_GROUP_ITEM)) {
            // Add active item permissions
            foreach ($object->permissions()->moduleItem()->get() as $permission) {
                $model = $permission->permissionable()->first();
                $moduleName = getModuleNameByModel($model);
                $fields[$moduleName . '_' . $model->id . '_permission'] = $permission->name;
            }
        }

        // Add active subdomain permissions
        foreach ($object->subdomains_access ?? [] as $subdomain) {
            $fields['subdomain_access_' . $subdomain] = true;
        }

        return $fields;
    }

    /**
     * Function executed after save on group form
     *
     * @param Model|Group $object
     * @param array $fields
     */
    public function afterSaveHandleGroupPermissions($object, $fields)
    {
        // Assign global permissions
        foreach (Permission::available(Permission::SCOPE_GLOBAL) as $permissionName) {
            if (isset($fields[$permissionName]) && $fields[$permissionName] === true) {
                $object->grantGlobalPermission($permissionName);
            } else {
                $object->revokeGlobalPermission($permissionName);
            }
        }

        $subdomainsAccess = [];

        // Assign item permissions + subdomain permission
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
            } elseif (Str::endsWith($key, '_permission')) {
                $item_name = explode('_', $key)[0];
                $item_id = explode('_', $key)[1];
                $item = getRepositoryByModuleName($item_name)->getById($item_id);

                // Only permissionName existed, do update or create
                if ($permissionName) {
                    $object->grantModuleItemPermission($permissionName, $item);
                } else {
                    $object->revokeModuleItemAllPermissions($item);
                }
            } elseif (Str::startsWith($key, 'subdomain_access_') && $permissionName) {
                array_push($subdomainsAccess, substr($key, strlen('subdomain_access_')));
            }
        }

        $object->subdomains_access = $subdomainsAccess;
        $object->save();
    }
}
