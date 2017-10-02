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

    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit Auth configuration
    |--------------------------------------------------------------------------
    |
    | Right now this only allows you to redefine the default login redirect path.
    |
     */
    'auth_login_redirect_path' => '/',

    'templates_on_frontend_domain' => false,
];
