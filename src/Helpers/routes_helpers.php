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

if (!function_exists('pageRoute')) {
    function pageRoute($key, $prefix, $update = false)
    {
        $routeName = 'admin.' . ($prefix ? $prefix . '.' : '') . $key;

        if ($update) {
            $routeName .= '.update';
        }

        return route($routeName);
    }
}
