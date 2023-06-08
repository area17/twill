<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

if (! function_exists('moduleRoute')) {
    function moduleRoute(
        string $moduleName,
        ?string $prefix = null,
        string $action = '',
        array|int|string $parameters = [],
        bool $absolute = true
    ): string {
        // Fix module name case
        $moduleName = Str::camel($moduleName);

        // Nested module, pass in current parameters for deeply nested modules
        if (Str::contains($moduleName, '.')) {
            $parameters = array_merge(Route::current()->parameters(), $parameters);
        }

        // Create base route name
				$routeName = config('twill.admin_route_name_prefix') . ($prefix ? $prefix . '.' : '');

        // Prefix it with module name only if prefix doesn't contains it already
        if (
            config('twill.allow_duplicates_on_route_names', true) ||
            ($prefix !== $moduleName &&
                ! Str::endsWith($prefix, '.' . $moduleName))
        ) {
            $routeName .= $moduleName;
        }

        $glue = Str::endsWith($routeName, '.') ? '' : '.';

        //  Add the action name
        $routeName .= $action ? "{$glue}{$action}" : '';

        // Build the route
        return route($routeName, $parameters, $absolute);
    }
}
