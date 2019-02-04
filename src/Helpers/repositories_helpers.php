<?php

if (!function_exists('getRepositoryByModuleName')) {
    function getRepositoryByModuleName($moduleName)
    {
        return app(config('twill.namespace') . "\Repositories\\" . ucfirst(str_singular($moduleName)) . "Repository");
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