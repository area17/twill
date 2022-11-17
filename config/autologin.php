<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auto login
    |--------------------------------------------------------------------------
    |
    */
    'enabled' => env('APP_DEBUG'),
    'environments' => ['local'],
    'email' => env('TWILL_AUTO_LOGIN_EMAIL'),
    'password' => env('TWILL_AUTO_LOGIN_PASSWORD'),
];
