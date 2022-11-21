<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auto login
    |--------------------------------------------------------------------------
    |
    */
    'enabled' => env('TWILL_AUTO_LOGIN_ENABLED', false),
    'environments' => ['local'],
    'email' => env('TWILL_AUTO_LOGIN_EMAIL'),
    'password' => env('TWILL_AUTO_LOGIN_PASSWORD'),
];
