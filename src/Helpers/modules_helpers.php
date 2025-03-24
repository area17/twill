<?php

use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Support\Str;
use A17\Twill\Models\Permission;
use A17\Twill\Repositories\ModuleRepository;
use Illuminate\Filesystem\Filesystem;
use A17\Twill\Facades\TwillCapsules;
use A17\Twill\Exceptions\ModuleNotFoundException;
use A17\Twill\Exceptions\NoCapsuleFoundException;

if (!function_exists('getAllModules')) {
    function getAllModules()
    {
        $repositories = collect(app(FileSystem::class)->glob(app_path('Repositories') . '/*.php'))->map(function ($repository) {
            $re = "/(?<=Repositories\/).+(?=\.php)/";
            preg_match($re, $repository, $matches);

            return config('twill.namespace') . '\\Repositories\\' . $matches[0];
        });

        $moduleRepositories = $repositories->filter(function ($repository) {
            return is_subclass_of($repository, ModuleRepository::class);
        });

        return $moduleRepositories->map(function ($repository) {
            $modelName = str_replace(array('App\\Repositories\\', 'Repository'), '', $repository);

            return Str::plural(lcfirst($modelName));
        });
    }
}

if (!function_exists('getModelByModuleName')) {
    function getModelByModuleName($moduleName)
    {
        $model = config('twill.namespace') . '\\Models\\' . Str::studly(Str::singular($moduleName));

        if (!class_exists($model)) {
            try {
                $model = TwillCapsules::getCapsuleForModule($moduleName)->getModel();
            } catch (NoCapsuleFoundException) {
                throw new Exception($model . ' not found');
            }
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
        if ($relation instanceof TwillModelContract) {
            $model = get_class($relation);
        }
        if (!$model) {
            $model = ucfirst(Str::singular($relation));
        }
        if ($model instanceof TwillModelContract) {
            $model = get_class($model);
        }
        $model = class_basename($model);

        $repository = config('twill.namespace') . '\\Repositories\\' . $model . 'Repository';

        if (!class_exists($repository)) {
            try {
                $repository = TwillCapsules::getCapsuleForModel($model)->getRepositoryClass();
            } catch (NoCapsuleFoundException) {
                throw new ModuleNotFoundException($repository . ' not found');
            }
        }

        return app($repository);
    }
}

if (!function_exists('getModelController')) {
    function getModelController(TwillModelContract $model)
    {
        $modelName = Str::afterLast($model::class, '\\');

        $controller = config('twill.namespace') . '\\Http\\Controllers\\Twill\\' . $modelName . 'Controller';

        if (!class_exists($controller)) {
            try {
                $controller = TwillCapsules::getCapsuleForModel($model)->getControllerClass();
            } catch (NoCapsuleFoundException) {
                throw new Exception($controller . ' not found');
            }
        }

        return app($controller);
    }
}

if (!function_exists('updatePermissionOptions')) {
    function updatePermissionOptions($options, $user, $item)
    {
        $permissions = [];

        if ($user->role) {
            $permissions = $user->role->permissions()->module()->pluck('name', 'permissionable_type')->all();
            if (empty($permissions) && $user->role->permissions()->global()->where('name', 'manage-modules')->first()) {
                $permissions[get_class($item)] = 'manage-item';
            }
        }

        // looking for group permissions belonging to the user
        foreach ($user->publishedGroups as $group) {
            if (($permission = $group->permissions()->ofItem($item)->first()) !== null) {
                if (isset($permissions[get_class($item)])) {
                    $scopes = Permission::available(Permission::SCOPE_ITEM);
                    $previous = array_search($permissions[get_class($item)], $scopes);
                    $current = array_search($permission->name, $scopes);

                    // check permission level
                    if ($current > $previous) {
                        $permissions[get_class($item)] = $permission->name;
                    }
                } else {
                    $permissions[get_class($item)] = $permission->name;
                }
            }
        }

        if (isset($permissions[get_class($item)])) {
            $globalPermission = str_replace('-module', '-item', $permissions[get_class($item)]);
            foreach ($options as &$option) {
                if ($option['value'] !== $globalPermission || $globalPermission === 'manage-item') {
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
        foreach ($user->publishedGroups as $group) {
            if (in_array($permission, $group->permissions()->ofItem($item)->get()->pluck('name')->all())) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('isUserGroupPermissionModuleExists')) {
    function isUserGroupPermissionModuleExists($user, $moduleName, $permission)
    {
        foreach ($user->publishedGroups as $group) {
            if ($moduleName === 'global') {
                return $group->permissions()->global()->where('name', 'manage-modules')->exists();
            }

            if (in_array($permission, $group->permissions()->OfModuleName($moduleName)->get()->pluck('name')->all())) {
                return true;
            }
        }

        return false;
    }
}
