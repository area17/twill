<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Services default configuration values
    |--------------------------------------------------------------------------
    |
    | Set of default values. These can be replaced in your config/services.php
    |
     */
    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_CALLBACK_URL', 'login/oauth/callback/github')
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_CALLBACK_URL', '/login/oauth/callback/google')
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_CALLBACK_URL', 'login/oauth/callback/facebook')
    ],

];
