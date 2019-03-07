<?php

use A17\Twill\Models\Permission;
use Illuminate\Filesystem\Filesystem;

if (!function_exists('getModelByModuleName')) {
    function getModelByModuleName($moduleName)
    {
        return config('twill.namespace') . '\\Models\\' . studly_case(str_singular($moduleName));
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

        return app(config('twill.namespace') . "\\Repositories\\" . ucfirst($model) . "Repository");
    }
}

if (!function_exists('getAllModules')) {
    function getAllModules()
    {
        $repositories = collect(app(FileSystem::class)->glob(app_path('Repositories') . '/*.php'))->map(function ($repository) {
            $re = "/(?<=Repositories\/).+(?=\.php)/";
            preg_match($re, $repository, $matches);
            return config('twill.namespace') . "\\Repositories\\" . $matches[0];
        });

        $moduleRepositories = $repositories->filter(function ($repository) {
            return is_subclass_of($repository, 'A17\Twill\Repositories\ModuleRepository');
        });

        $modules = $moduleRepositories->map(function ($repository) {
            $modelName = str_replace("Repository", '', str_replace(config('twill.namespace') . "\\Repositories\\", "", $repository));
            return str_plural(lcfirst($modelName));
        });

        return $modules;
    }
}

if (!function_exists('isPermissionableModule')) {
    function isPermissionableModule($moduleName)
    {
        return Permission::permissionableModules()->contains($moduleName);
    }
}
