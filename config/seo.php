<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twill SEO configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to provide the package with some SEO configuration
    | for the frontend site controller helper and image service.
    |
     */
    'site_title' => config('app.name'),
    'site_desc' => config('app.name'),
    'image_default_id' => env('SEO_IMAGE_DEFAULT_ID'),
    'image_local_fallback' => env('SEO_IMAGE_LOCAL_FALLBACK'),
];
