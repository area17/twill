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
    'use_inspector' => env('DEBUG_USE_INSPECTOR', false),
    'debug_bar_in_fe' => env('DEBUG_BAR_IN_FE', false),

];
