<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;

if (!function_exists('moduleRoute')) {
    /**
     * @param string $moduleName
     * @param string $prefix
     * @param string $action
     * @param array $parameters
     * @param bool $absolute
     * @return string
     */
    function moduleRoute($moduleName, $prefix, $action, $parameters = [], $absolute = true)
    {
        $routeName = 'admin.' . ($prefix ? $prefix . '.' : '') . camel_case($moduleName) . '.' . $action;
        return route($routeName, $parameters, $absolute);
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

        if ($isModule) {
            $action = $element['route'] ?? 'index';
            return moduleRoute($key, $prefix, $action);
        } elseif ($element['raw'] ?? false) {
            return !empty($element['route']) ? $element['route'] : '#';
        }

        return !empty($element['route']) ? route($element['route'], $element['params'] ?? []) : '#';
    }

}

if (!function_exists('isActiveNavigation')) {
    /**
     * @param array $navigationElement
     * @param string $navigationKey
     * @param string $activeNavigationKey
     * @return bool
     */
    function isActiveNavigation($navigationElement, $navigationKey, $activeNavigationKey)
    {
        $keysAreMatching = isset($activeNavigationKey) && $navigationKey === $activeNavigationKey;

        if ($keysAreMatching) {
            return true;
        }

        $urlsAreMatching = ($navigationElement['raw'] ?? false) && Str::endsWith(Request::url(), $navigationElement['route']);

        return $urlsAreMatching;
    }
}
