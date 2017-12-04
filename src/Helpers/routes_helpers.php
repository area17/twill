<?php

if (!function_exists('moduleRoute')) {
    function moduleRoute($moduleName, $prefix, $action, $parameters = [])
    {
        $routeName = 'admin.' . ($prefix ? $prefix . '.' : '') . camel_case($moduleName) . '.' . $action;
        return route($routeName, $parameters);
    }
}

if (!function_exists('getNavigationUrl')) {
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
    function isActiveNavigation($navigationElement, $navigationKey, $activeNavigationKey)
    {
        $keysAreMatching = isset($activeNavigationKey) && $navigationKey === $activeNavigationKey;

        if ($keysAreMatching) {
            return true;
        }

        $urlsAreMatching = ($navigationElement['raw'] ?? false) && Request::url() == $navigationElement['route'];

        return $urlsAreMatching;
    }
}
