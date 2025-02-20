<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twill Glide configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to provide the package with your configuration
    | for the Glide image service.
    |
     */

    'source' => env('GLIDE_SOURCE', storage_path('app/public/' . config('twill.media_library.local_path'))),
    'use_source_disk' => env('GLIDE_USE_SOURCE_DISK', false),
    'source_disk' => env('GLIDE_SOURCE_DISK', 'twill_media_library'),
    'source_path_prefix' => env('GLIDE_SOURCE_PATH_PREFIX', null),
    'cache' => env('GLIDE_CACHE', storage_path('app')),
    'use_cache_disk' => env('GLIDE_USE_CACHE_DISK', false),
    'cache_disk' => env('GLIDE_CACHE_DISK', 'twill_media_library'),
    'cache_path_prefix' => env('GLIDE_CACHE_PATH_PREFIX', 'glide_cache'),
    'base_url' => env('GLIDE_BASE_URL', config('app.url')),
    'base_path' => env('GLIDE_BASE_PATH', 'img'),
    'use_signed_urls' => env('GLIDE_USE_SIGNED_URLS', false),
    'sign_key' => env('GLIDE_SIGN_KEY'),
    'driver' => env('GLIDE_DRIVER', 'gd'),
    'add_params_to_svgs' => false,
    'default_params' => [
        'fm' => 'jpg',
        'q' => '80',
        'fit' => 'max',
    ],
    'lqip_default_params' => [
        'fm' => 'gif',
        'blur' => 100,
        'dpr' => 1,
    ],
    'social_default_params' => [
        'fm' => 'jpg',
        'w' => 900,
        'h' => 470,
        'fit' => 'crop',
    ],
    'cms_default_params' => [
        'q' => '60',
        'dpr' => '1',
    ],
    'presets' => [],
    'original_media_for_extensions' => ['svg'],
    'use_streamed_response_for_original_media' => env('GLIDE_USE_STREAMED_RESPONSE_FOR_ORIGINAL_MEDIA', false),
    'use_temporary_url_for_original_media' => env('GLIDE_USE_TEMPORARY_URL_FOR_ORIGINAL_MEDIA', false),
    'temporary_url_expiration' => env('GLIDE_TEMPORARY_URL_EXPIRATION', 3600), // seconds
    'keep_transparency' => env('GLIDE_KEEP_TRANSPARENCY', false),
];
