<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Namespace
    |--------------------------------------------------------------------------
    |
    | This value is the namespace of your application.
    |
     */
    'namespace' => 'App',

    /*
    |--------------------------------------------------------------------------
    | Application Admin URL
    |--------------------------------------------------------------------------
    |
    | This value is the URL of your admin application.
    |
     */
    'admin_app_url' => env('ADMIN_APP_URL', 'admin.' . env('APP_URL')),

    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit Enabled Features
    |--------------------------------------------------------------------------
    |
    | This array allows you to enable/disable the CMS Toolkit default features.
    |
     */
    'enabled' => [
        'users-management' => true,
        'media-library' => true,
        'file-library' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit Auth configuration
    |--------------------------------------------------------------------------
    |
    | Right now this array only allows you to redefine the
    | default login redirect path.
    |
     */
    'auth' => [
        'login_redirect_path' => '/',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit Media Library configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to provide the package with your configuration
    | for the media library disk, endpoint type and others options depending
    | on your endpoint type.
    |
    | Supported endpoint types: 'local' and 's3'.
    | Set cascade_delete to true to delete files on the storage too when
    | deleting from the media library.
    | If using the 'local' endpoint type, define a 'local_path' to store files.
    | Supported image service: 'A17\CmsToolkit\Services\MediaLibrary\Imgix'
    |
     */
    'media_library' => [
        'disk' => 'libraries',
        'endpoint_type' => env('MEDIA_LIBRARY_ENDPOINT_TYPE', 's3'),
        'cascade_delete' => env('MEDIA_LIBRARY_CASCADE_DELETE', false),
        'local_path' => env('MEDIA_LIBRARY_LOCAL_PATH'),
        'image_service' => 'A17\CmsToolkit\Services\MediaLibrary\Imgix',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit Imgix configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to provide the package with your configuration
    | for the Imgix image service.
    |
     */
    'imgix' => [
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
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit File Library configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to provide the package with your configuration
    | for the file library disk, endpoint type and others options depending
    | on your endpoint type.
    |
    | Supported endpoint types: 'local' and 's3'.
    | Set cascade_delete to true to delete files on the storage too when
    | deleting from the file library.
    | If using the 'local' endpoint type, define a 'local_path' to store files.
    |
     */
    'file_library' => [
        'disk' => 'libraries',
        'endpoint_type' => env('FILE_LIBRARY_ENDPOINT_TYPE', 's3'),
        'cascade_delete' => env('FILE_LIBRARY_CASCADE_DELETE', false),
        'local_path' => env('FILE_LIBRARY_LOCAL_PATH'),
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit Block Editor configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to provide the package with your configuration
    | for the Block renderer service.
    | More to come here...
    |
     */
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

    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit SEO configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to provide the package with some SEO configuration
    | for the frontend site controller helper and image service.
    |
     */
    'seo' => [
        'site_title' => config('app.name'),
        'site_desc' => config('app.name'),
        'image_default_id' => env('SEO_IMAGE_DEFAULT_ID'),
        'image_local_fallback' => env('SEO_IMAGE_LOCAL_FALLBACK'),
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit Developer configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to enable/disable debug tool and configurations.
    |
     */
    'debug' => [
        'use_whoops' => env('DEBUG_USE_WHOOPS', true),
        'whoops_path_guest' => env('WHOOPS_GUEST_PATH'),
        'whoops_path_host' => env('WHOOPS_HOST_PATH'),
        'debug_use_inspector' => env('DEBUG_USE_INSPECTOR', false),
        'debug_bar_in_fe' => env('DEBUG_BAR_IN_FE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit Frontend assets configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to setup frontend helpers related settings.
    |
     */
    'frontend' => [
        'rev_manifest_path' => public_path('dist/rev-manifest.json'),
        'dev_assets_path' => url('dev'),
        'dist_assets_path' => url('dist'),
        'svg_sprites_path' => 'sprites.svg', // relative to dev/dist assets paths
        'svg_sprites_use_hash_only' => true,
    ],
];
