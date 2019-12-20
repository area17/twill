<?php

use A17\Twill\Models\Permission;
use Illuminate\Filesystem\Filesystem;

if (!function_exists('getModelByModuleName')) {
    function getModelByModuleName($moduleName)
    {
        $model = config('twill.namespace') . '\\Models\\' . studly_case(str_singular($moduleName));
        if (!class_exists($model)) {
            throw new Exception($model . ' not existed');
        }
        return $model;
    }
}

if (!function_exists('getModuleNameByModel')) {
    function getModuleNameByModel($model)
    {
        return str_plural(lcfirst(class_basename($model)));
    }
}

if (!function_exists('getRepositoryByModuleName')) {
    function getRepositoryByModuleName($moduleName)
    {
        return getModelRepository(class_basename(getModelByModuleName($moduleName)));
    }
}

if (!function_exists('getModelRepository')) {
    function getModelRepository($relation, $model = null)
    {
        if (!$model) {
            $model = ucfirst(str_singular($relation));
        }
        $repository = config('twill.namespace') . "\\Repositories\\" . ucfirst($model) . "Repository";

        if (!class_exists($repository)) {
            throw new Exception($repository . ' not existed');
        }
        return app($repository);
    }
}

if (!function_exists('isPermissionableModule')) {
    // return the module name if is permissionable module, otherwise return false
    function isPermissionableModule($moduleName)
    {
        $submodule = Permission::permissionableModules()->filter(function($module) use ($moduleName) {
            return strpos($module, '.') && explode('.', $module)[1] === $moduleName;
        })->first();

        if (Permission::permissionableModules()->contains($moduleName)) {
            return $moduleName;
        } elseif ($submodule) {
            return $submodule;
        } else {
            return false;
        }
    }
}

if (!function_exists('updatePermissionOptions')) {
    // return the module name if is permissionable module, otherwise return false
    function updatePermissionOptions($options, $user, $item)
    {

        $permissions = $user->role->permissions()->module()->pluck('name','permissionable_type')->all();
        if (empty($permissions)) {
            if ($user->role->permissions()->global()->where('name', 'manage-modules')->first()){
                $permissions[get_class($item)] = 'manage-item';
            }

        }
        if (isset($permissions[get_class($item)])) {
            $globalPermission = str_replace('-module', '-item', $permissions[get_class($item)]);
            foreach($options as &$option) {
                if ($option['value'] != $globalPermission || $globalPermission=='manage-item') {
                    $option['disabled'] = true;
                } else {
                    break;
                }
            }
        }
        return $options;
    }
}
