<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twill Imgix configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to provide the package with your configuration
    | for the Imgix image service.
    |
     */
    'source_host' => env('IMGIX_SOURCE_HOST'),
    'use_https' => env('IMGIX_USE_HTTPS', true),
    'use_signed_urls' => env('IMGIX_USE_SIGNED_URLS', false),
    'sign_key' => env('IMGIX_SIGN_KEY'),
    'default_params' => [
        'fm' => 'jpg',
        'q' => '80',
        'auto' => 'compress,format',
        'fit' => 'min',
    ],
    'lqip_default_params' => [
        'fm' => 'gif',
        'auto' => 'compress',
        'blur' => 100,
        'dpr' => 1,
    ],
    'social_default_params' => [
        'fm' => 'jpg',
        'w' => 900,
        'h' => 470,
        'fit' => 'crop',
        'crop' => 'entropy',
    ],
    'cms_default_params' => [
        'q' => 60,
        'dpr' => 1,
    ],
];
