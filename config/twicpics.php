<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TwicPics Domain
    |--------------------------------------------------------------------------
    |
    | The full domain of your TwicPics account, e.g. `m1lz5gkt.twic.pics`.
    |
    | @see https://www.twicpics.com/docs/getting-started/subdomain
    |
    */
    'domain' => env('TWICPICS_DOMAIN', ''),

    /*
    |--------------------------------------------------------------------------
    | TwicPics Path
    |--------------------------------------------------------------------------
    |
    | The single path prefix configured in your TwicPics account, e.g `images`.
    |
    | @see https://www.twicpics.com/docs/getting-started/subdomain
    |
    */
    'path' => env('TWICPICS_PATH', ''),

    /*
    |--------------------------------------------------------------------------
    | TwicPics API Version
    |--------------------------------------------------------------------------
    |
    | The API version used for image manipulations parameters.
    |
    | @see https://www.twicpics.com/docs/api/basics
    |
    */
    'api_version' => env('TWICPICS_API_VERSION', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | TwicPics Default Parameters
    |--------------------------------------------------------------------------
    |
    | The default image manipulation parameters.
    |
    | @see https://www.twicpics.com/docs/api/manipulations
    |
    */
    'default_params' => [
        'quality' => '80',
    ],
    'lqip_default_params' => [
        'output' => 'preview',
    ],
    'social_default_params' => [
        // the 'crop' value will likely be replaced by the user selected value but it
        // must be declared before 'cover' here to be a valid TwicPics manipulation
        'crop' => '900x470',
        'cover' => '900x470',
    ],
    'cms_default_params' => [
        'quality' => 60,
    ],
];
