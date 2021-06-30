<?php

namespace A17\Twill\Repositories\Behaviors;

use Illuminate\Support\Str;
use A17\Twill\Models\Permission;

trait HandleGroupPermissions
{
    public function getFormFieldsHandleGroupPermissions($object, $fields)
    {
        $fields = $this->renderGroupPermissions($object, $fields);

        return $fields;
    }

    public function afterSaveHandleGroupPermissions($object, $fields)
    {
        $this->handleGroupPermissions($object, $fields);
    }

    protected function handleGroupPermissions($group, $fields)
    {
        foreach (Permission::available(Permission::SCOPE_GLOBAL) as $permissionName) {
            if (isset($fields[$permissionName]) && $fields[$permissionName] === true) {
                $group->grantGlobalPermission($permissionName);
            } else {
                $group->revokeGlobalPermission($permissionName);
            }
        }

        $subdomainsAccess = [];



        foreach ($fields as $key => $permissionName) {
            //Used for the roleGroup mode
            if (Str::startsWith($key, 'module_') && Str::endsWith($key, '_permissions')) {
                $modulePermissions = Permission::available(Permission::SCOPE_MODULE);
                $model = getModelByModuleName($moduleName = explode('_', $key)[1]);

                $currentPermission = $group->permissions()
                    ->where('permissionable_type', $model)
                    ->whereIn('name', $modulePermissions)
                    ->first()
                ;

                if (!$currentPermission || $permissionName != $currentPermission->name) {
                    $group->revokeAllModulePermission($model);
                    if (in_array($permissionName, $modulePermissions)) {
                        $group->grantModulePermission($permissionName, $model);
                    }
                }
            } elseif (Str::endsWith($key, '_permission')) {
                $item_name = explode('_', $key)[0];
                $item_id = explode('_', $key)[1];
                $item = getRepositoryByModuleName($item_name)->getById($item_id);

                // Only permissionName existed, do update or create
                if ($permissionName) {
                    $group->grantModuleItemPermission($permissionName, $item);
                } else {
                    $group->revokeModuleItemAllPermissions($item);
                }
            } elseif (Str::startsWith($key, 'subdomain_access_') && $permissionName) {
                array_push($subdomainsAccess, substr($key, strlen('subdomain_access_')));
            }
        }

        $group->subdomains_access = $subdomainsAccess;
        $group->save();
    }

    protected function renderGroupPermissions($group, $fields)
    {
        $fields = [];

        if (\Config::get('twill.permission.level') == 'roleGroup') {
            foreach (Permission::permissionableModules() as $moduleName) {
                $modulePermission = $group->permissions()->module()->ofModuleName($moduleName)->first();
                if ($modulePermission) {
                    $fields['module_' . $moduleName . '_permissions'] = $modulePermission->name;
                } else {
                    $fields['module_' . $moduleName . '_permissions'] = 'none';
                }
            }
        } elseif (\Config::get('twill.permission.level') == 'roleGroupModule') {
            #looking for item permissions
            foreach ($group->permissions()->moduleItem()->get() as $permission) {
                $model = $permission->permissionable()->first();
                $moduleName = getModuleNameByModel($model);
                $fields[$moduleName . '_' . $model->id . '_permission'] = $permission->name;
            }
        }

        foreach ($group->subdomains_access ?? [] as $subdomain) {
            $fields['subdomain_access_' . $subdomain] = true;
        }

        return $fields;
    }
}
