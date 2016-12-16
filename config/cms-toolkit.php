<?php

return [
    'namespace' => 'App',

    'admin_app_url' => env('ADMIN_APP_URL', 'admin.' . env('APP_URL')),

    'auth' => [
        'login_redirect_path' => '/',
    ],

    'enabled' => [
        'media-library' => true,
        'file-library' => true,
    ],

    'media_library' => [
        'disk' => 's3',
        'endpoint_type' => env('MEDIA_LIBRARY_ENDPOINT_TYPE', 's3'),
        'cascade_delete' => env('MEDIA_LIBRARY_CASCADE_DELETE', false),
        'local_path' => env('MEDIA_LIBRARY_LOCAL_PATH'),
        'image_service' => "A17\CmsToolkit\Services\MediaLibrary\Imgix",
    ],

    'imgix' => [
        'source_host' => env('IMGIX_SOURCE_HOST'),
        'use_https' => env('IMGIX_USE_HTTPS', true),
        'use_signed_urls' => env('IMGIX_USE_SIGNED_URLS', false),
        'sign_key' => env('IMGIX_SIGN_KEY'),

        'default_params' => ['fm' => 'jpg', 'q' => '80', 'auto' => 'compress,format', 'fit' => 'min'],
        'lqip_default_params' => ['fm' => 'gif', 'auto' => 'compress', 'blur' => 100, 'dpr' => 1],
        'social_default_params' => ['fm' => 'jpg', 'w' => 900, 'h' => 470, 'fit' => 'crop', 'crop' => 'entropy'],
        'cms_default_params' => ['q' => 60, 'dpr' => 1],
    ],

    'file_library' => [
        'disk' => 's3',
        'endpoint_type' => env('FILE_LIBRARY_ENDPOINT_TYPE', 's3'),
        'cascade_delete' => env('FILE_LIBRARY_CASCADE_DELETE', false),
        'local_path' => env('FILE_LIBRARY_LOCAL_PATH'),
    ],

    'block_editor' => [
        'blocks' => [
            "blocktext" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Text",
            "blockquote" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Text",
            "blocktitle" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Text",
            "imagefull" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
            "imagesimple" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
            "imagegrid" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
            "imagetext" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
            "diaporama" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
            "blockseparator" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Separator",
        ],
        'sitemap_blocks' => [
            'A17\CmsToolkit\Services\BlockEditor\Blocks\Image',
        ],
    ],

    'seo' => [
        'site_title' => config('app.name'),
        'site_title' => config('app.name'),
        'image_default_id' => env('SEO_IMAGE_DEFAULT_ID'),
        'image_local_fallback' => env('SEO_IMAGE_LOCAL_FALLBACK'),
    ],

    'debug' => [
        'use_whoops' => env('DEBUG_USE_WHOOPS', true),
        'whoops_path_guest' => env('WHOOPS_GUEST_PATH'),
        'whoops_path_host' => env('WHOOPS_HOST_PATH'),
        'debug_use_inspector' => env('DEBUG_USE_INSPECTOR', false),
        'debug_bar_in_fe' => env('DEBUG_BAR_IN_FE', false),
    ],
];
