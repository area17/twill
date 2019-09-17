<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Twill Developer configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to enable/disable debug tool and configurations.
    |
     */
    'use_whoops' => env('DEBUG_USE_WHOOPS', true),
    'whoops_path_guest' => env('WHOOPS_GUEST_PATH'),
    'whoops_path_host' => env('WHOOPS_HOST_PATH'),
];
