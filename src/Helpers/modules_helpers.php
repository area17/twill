<?php

use A17\Twill\Models\Permission;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

if (!function_exists('getModelByModuleName')) {
    function getModelByModuleName($moduleName)
    {
        $model = config('twill.namespace') . '\\Models\\' . Str::studly(Str::singular($moduleName));
        if (!class_exists($model)) {
            throw new Exception($model . ' not existed');
        }
        return $model;
    }
}

if (!function_exists('getModuleNameByModel')) {
    function getModuleNameByModel($model)
    {
        return Str::plural(lcfirst(class_basename($model)));
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
            $model = ucfirst(Str::singular($relation));
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
        $permissions = [];
        if ($user->role) {
            $permissions = $user->role->permissions()->module()->pluck('name','permissionable_type')->all();
            if (empty($permissions)) {
                if ($user->role->permissions()->global()->where('name', 'manage-modules')->first()){
                    $permissions[get_class($item)] = 'manage-item';
                }
            }
        }

        #looking for group permissions belongs to the user
        foreach($user->publishedGroups as $group) {
            if (($permission=$group->permissions()->OfItem($item)->first())!= null) {
                if (isset($permissions[get_class($item)])) {
                    $scopes = Permission::available('item');
                    $previous = array_search($permissions[get_class($item)], $scopes);
                    $current = array_search($permission->name, $scopes);
                    #check permission level
                    if ($current > $previous) {
                        $permissions[get_class($item)] = $permission->name;
                    }

                } else {
                    $permissions[get_class($item)] = $permission->name;
                }
            }
        }

        //
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

if (!function_exists('updatePermissionGroupOptions')) {
    function updatePermissionGroupOptions($options, $item, $group)
    {
        return $options;
    }
}

if (!function_exists('isUserGroupPermissionItemExists')) {
    function isUserGroupPermissionItemExists($user, $item, $permission)
    {
        foreach($user->publishedGroups as $group) {
            if (in_array($permission, $group->permissions()->OfItem($item)->get()->pluck('name')->all())){
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('isUserGroupPermissionModuleExists')) {
    function isUserGroupPermissionModuleExists($user, $moduleName, $permission)
    {
        foreach($user->publishedGroups as $group) {
            if ($moduleName=='global') {
                return $group->permissions()->global()->where('name', 'manage-modules')->exists();
            } else {
                if( in_array($permission, $group->permissions()->OfModuleName($moduleName)->get()->pluck('name')->all())){
                    return true;
                }
            }

        }

        return false;
    }
}


