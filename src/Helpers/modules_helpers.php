<?php

use A17\Twill\Models\Permission;
use Illuminate\Filesystem\Filesystem;

if (!function_exists('getModelByModuleName')) {
    function getModelByModuleName($moduleName)
    {
        $model = config('twill.namespace') . '\\Models\\' . studly_case(str_singular($moduleName));
        if (!file_exists($model)) {
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

        if (!file_exists($repository)) {
            throw new Exception($repository . ' not existed');
        }
        return app($repository);
    }
}

if (!function_exists('isPermissionableModule')) {
    function isPermissionableModule($moduleName)
    {
        return Permission::permissionableModules()->contains($moduleName);
    }
}
