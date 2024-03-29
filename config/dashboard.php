<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twill Dashboard configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to provide the package with your configuration
    | for the dashboard.
    |
     */
    'modules' => [],
    'analytics' => ['enabled' => false],
    'search_endpoint' => config('twill.admin_route_name_prefix') . 'search',

    /*
    |--------------------------------------------------------------------------
    | Twill Auth activity related configuration
    |--------------------------------------------------------------------------
    |
     */
    'auth_activity_log' => [
        'login' => false,
        'logout' => false,
    ],
    'auth_activity_causer' => 'users',
];
