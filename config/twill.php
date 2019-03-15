<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Namespace
    |--------------------------------------------------------------------------
    |
    | This value is the namespace of your application.
    |
     */
    'namespace' => 'App',

    /*
    |--------------------------------------------------------------------------
    | Application Admin URL
    |--------------------------------------------------------------------------
    |
    | This value is the URL of your admin application.
    |
     */
    'admin_app_url' => env('ADMIN_APP_URL', 'admin.' . env('APP_URL')),
    'admin_app_path' => env('ADMIN_APP_PATH', ''),

    /*
    |--------------------------------------------------------------------------
    | Application Admin Route and domain pattern
    |--------------------------------------------------------------------------
    |
    | This value add some patterns for the domain and routes of the admin.
    |
     */
    'admin_route_patterns' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | Twill middleware group configuration
    |--------------------------------------------------------------------------
    |
    | Right now this only allows you to redefine the default login redirect path.
    |
     */
    'admin_middleware_group' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Twill users tables configuration
    |--------------------------------------------------------------------------
    |
     */
    'users_table' => 'twill_users',
    'password_resets_table' => 'twill_password_resets',

    /*
    |--------------------------------------------------------------------------
    | Twill Auth related configuration
    |--------------------------------------------------------------------------
    |
     */
    'auth_login_redirect_path' => '/',

    'templates_on_frontend_domain' => false,

    'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
];
