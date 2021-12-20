<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

if (!function_exists('moduleRoute')) {
    /**
     * @param string $moduleName
     * @param string $prefix
     * @param string $action
     * @param array $parameters
     * @param bool $absolute
     * @return string
     */
    function moduleRoute($moduleName, $prefix, $action = '', $parameters = [], $absolute = true)
    {
        // Fix module name case
        $moduleName = Str::camel($moduleName);

        // Nested module, pass in current parameters for deeply nested modules
        if (Str::contains($moduleName, '.')) {
            $parameters = array_merge(Route::current()->parameters(), $parameters);
        }

        // Create base route name
        $routeName = 'admin.' . ($prefix ? $prefix . '.' : '');

        // Prefix it with module name only if prefix doesn't contains it already
        if (
            config('twill.allow_duplicates_on_route_names', true) ||
            ($prefix !== $moduleName &&
                !Str::endsWith($prefix, '.' . $moduleName))
        ) {
            $routeName .= "{$moduleName}";
        }

        //  Add the action name
        $routeName .= $action ? ".{$action}" : '';

        // Build the route
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

if (!function_exists('twillRouteGroupPrefix')) {
    function twillRouteGroupPrefix()
    {
        $groupPrefix = trim(
            str_replace('/', '.', Route::getLastGroupPrefix()),
            '.'
        );

        if (!empty(config('twill.admin_app_path'))) {
            $groupPrefix = ltrim(
                str_replace(
                    config('twill.admin_app_path'),
                    '',
                    $groupPrefix
                ),
                '.'
            );
        }

        return $groupPrefix;
    }
}

if (!function_exists('lastRouteGroupName')) {
    function lastRouteGroupName()
    {
        // Get the current route groups
        $routeGroups = Route::getGroupStack() ?? [];

        // Get the name prefix of the last group
        return end($routeGroups)['as'] ?? '';
    }
}
