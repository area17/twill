<?php

if (!function_exists('routeLocalized')) {
    function routeLocalized($name, $parameters = [], $force_zone = null, $force_locale = null, $absolute = false)
    {
        if (str_contains($name, '/')) {
            return $name;
        }

        if ($force_locale && in_array($force_locale, config('translatable.locales'))) {
            $route_name = $force_locale . '.' . $name;
        } else {
            $route_name = app()->getLocale() . '.' . $name;
        }

        return app('url')->route($route_name, $parameters, $absolute);
    }
}

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
    function isActiveNavigation($element, $key, $activeNavigation)
    {
        return (isset($activeNavigation) && $key === $activeNavigation) || (($element['raw'] ?? false) && Request::url() == $element['route']);
    }
}
