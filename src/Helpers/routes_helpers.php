<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

if (!function_exists('moduleRoute')) {
    /**
     * @deprecated use TwillRoutes::getModelRoute with the model class instead.
     */
    function moduleRoute(string $moduleName, ?string $prefix = null, string $action = '', array $parameters = [], bool $absolute = true)
    {
        $modelClass = $moduleName;
        // Get the class for the module name.
        if (!Str::contains($moduleName, '\\')) {
            // @todo: This wont work with capsules.
            // @todo: Dashboard refactor.
            $modelClass = getModelByModuleName($moduleName);
        }
        return \A17\Twill\Facades\TwillRoutes::getModelRoute($modelClass, $action, $parameters, $absolute, $prefix);
    }
}

if (!function_exists('getNavigationUrl')) {
    /**
     * @param array $element
     * @param string $key
     * @param string|null $prefix
     * @return string
     */
    function getNavigationUrl($element, $key, $prefix = null)
    {
        $isModule = $element['module'] ?? false;
        $isSingleton = $element['singleton'] ?? false;

        if ($isModule) {
            $action = $element['route'] ?? 'index';

            return moduleRoute($key, $prefix, $action);
        } elseif ($isSingleton) {
            return moduleRoute($key, $prefix);
        } elseif ($element['raw'] ?? false) {
            return !empty($element['route']) ? $element['route'] : '#';
        }

        return !empty($element['route']) ? route($element['route'], $element['params'] ?? []) : '#';
    }
}

if (!function_exists('isActiveNavigation')) {
    function isActiveNavigation(array $navigationElement, string $navigationKey, string $activeNavigationKey): bool
    {
        $keysAreMatching = $navigationKey === $activeNavigationKey;

        if ($keysAreMatching) {
            return true;
        }

        $urlsAreMatching = ($navigationElement['raw'] ?? false) && Str::endsWith(
                Request::url(),
                $navigationElement['route']
            );

        return $urlsAreMatching;
    }
}
