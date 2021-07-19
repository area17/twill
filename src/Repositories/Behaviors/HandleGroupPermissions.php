<?php

namespace A17\Twill\Repositories\Behaviors;

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
        if (Config::get('twill.permissions.level') == 'roleGroup') {
            foreach (Permission::permissionableModules() as $moduleName) {
                $modulePermission = $object->permissions()->module()->ofModuleName($moduleName)->first();
                if ($modulePermission) {
                    $fields['module_' . $moduleName . '_permissions'] = $modulePermission->name;
                } else {
                    $fields['module_' . $moduleName . '_permissions'] = 'none';
                }
            }
        } elseif (Config::get('twill.permissions.level') == 'roleGroupModule') {
            // looking for item permissions
            foreach ($object->permissions()->moduleItem()->get() as $permission) {
                $model = $permission->permissionable()->first();
                $moduleName = getModuleNameByModel($model);
                $fields[$moduleName . '_' . $model->id . '_permission'] = $permission->name;
            }
        }

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
        foreach (Permission::available(Permission::SCOPE_GLOBAL) as $permissionName) {
            if (isset($fields[$permissionName]) && $fields[$permissionName] === true) {
                $object->grantGlobalPermission($permissionName);
            } else {
                $object->revokeGlobalPermission($permissionName);
            }
        }

        $subdomainsAccess = [];

        foreach ($fields as $key => $permissionName) {
            // Used for the roleGroup mode
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
