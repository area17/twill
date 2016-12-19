<center>![](https://s28.postimg.org/x9hd3ftul/A17_logo.png)</center>

# Laravel CMS Toolkit Documentation

## Introduction

The CMS Toolkit is a Laravel Composer package to rapidly create and deploy a completely custom admin area for our clients websites that is highly functional, beautiful and easy to use.
It's a curation of all the features that were developed on custom admin areas since our switch to Laravel. The architecture, conventions and helpers it provides currently powers the [Opéra National de Paris 2015 website redesign](https://www.operadeparis.fr/) ([codebase](https://code.area17.com/opera/onp)), the [AREA 17 2016 website redesign](https://area17.com) ([codebase](https://code.area17.com/a17/site)) and the [THG 2016 redesign website](https://www.thg-paris.com/) ([codebase](https://code.area17.com/THG/site)). 

By default, with very little developer actions, it provides:
- a beautiful admin interface that really focuses on the editors needs, using the [A17 CMS UI Toolkit](http://cms3.dev.area17.com)
- user authentication, authorization and management
- a media library (powered by S3 or local storage and [Imgix](https://imgix.com) rendering)
- a file library (powered by S3 or local storage)
- a block editor renderer and default blocks
- the ability to art direct responsive images
- static templates automatic routing
- rapid new content types creation/edition/maintenance for developers
- content types translations and slugs management
- a production ready exceptions handler
- frontend site controller helpers for SEO and SPF/PJAX sites

In development, you can use it in any Laravel environment like [Valet](https://laravel.com/docs/5.3/valet) or [Homestead](https://laravel.com/docs/5.3/homestead), though in a client's project context, you would ideally run your application in a custom virtual machine or Docker environment that is as close as possible as your production environment (either through a custom `after.sh` config for Homestead or through an Ansible provisionned Vagrant box). For provisionning and deployments, check out the Ansible playbooks, Capistrano and Envoy recipes in the A17 and THG codebases.

This package is in very early stage as of December '16. Most of the code it provides has been battle tested over the last 2 years through thousands of Unfuddle tickets but you know how it is...

<center>![](https://s29.postimg.org/o6b3ol993/1482156815_da3316954cb18549771e55f01fed6851.jpg)</center>

I'm pretty sure most of the bugs you'll encounter will be related to the creation of this package and how it hooks into your application. Feel free to ping on HipChat anytime :)


## Install

Via Composer

```bash
composer require a17/laravel-cms-toolkit
```
Add the CMS Toolkit service provider in `config/app.php`:
```php
'providers' => [
    ...
    A17\CmsToolkit\CmsToolkitServiceProvider::class,
];
```

Setup your `.env` file (or let the [Laravel Env Validator](https://github.com/mathiasgrimm/laravel-env-validator) yell at you if you don't).

Run the install command
```bash
php artisan cms-toolkit:install
```

That's about it!


## Usage

### Static templates
Frontenders, you might often be the first users of this package in new Laravel apps when starting to work on static templates.

Creating Blade files in `views/templates` will make them directly accessible at `admin.domain.dev/templates/file-name`. 

Feel free to use all [Blade](https://laravel.com/docs/5.3/blade) features, extend a parent layout and cut out your views in partials, this will help a lot during integration.

Frontend assets should live in the `public/dist` folder along with a `rev-manifest.json` for compiled assets in production. Using the [A17 FE Boilerplate](https://code.area17.com/a17/fe-boilerplate) should handle that for you. 

Use the `revAsset('asset.{css|js})` helper in your templates to get assets URLs in any environment.

Use the `icon('icon-name', [])` helper to display an icon from the SVG sprite. The second parameter is an array of options. It currently understand `title`, `role` and `css_class`.


### Configuration
#### The cms-toolkit configuration file

By default, you shouldn't have to modify anything if you want to use the default config which is basically:
- users management
- media library on S3 with Imgix
- file library on S3

The only thing you would have to do is setting up the necessary environment variables in your `.env` file.

You can override any of these configurations values independendtly from the empty `config/cms-toolkit.php` file that was published in your app when you ran the `cms-toolkit:install` command.

```php
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
        'disk' => 's3',
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
        'disk' => 's3',
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
        'site_title' => config('app.name'),
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
];

```


#### The cms-navigation configuration file

### User management
#### Roles
#### Permissions
#### Can Middleware

### Media Library
#### Storage provider
#### Image Rendering Service
#### Role & Crop params
#### File library
#### S3 direct upload

Create a IAM user for full access to the bucket and use its credentials in your `.env` file. You can use the following IAM permission:
```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": "s3:*",
            "Resource": [
                "arn:aws:s3:::YOUR_BUCKER_IDENTIFIER/*",
                "arn:aws:s3:::YOUR_BUCKER_IDENTIFIER"
            ]
        }
    ]
}
```
Create a IAM user for Imgix (or any other similar service) with only read-only access to your bucket and use its credentials to create an S3 source. You can use the following IAM permission:
```json
{
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:GetObject",
                "s3:ListBucket",
                "s3:GetBucketLocation"
            ],
            "Resource": [
                "arn:aws:s3:::YOUR_BUCKER_IDENTIFIER/*",
                "arn:aws:s3:::YOUR_BUCKER_IDENTIFIER"
            ]
        }
    ]
}
```
For improved security, modify the bucket CORS configuration to accept uploads request from your admin domain only:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<CORSConfiguration xmlns="http://s3.amazonaws.com/doc/2006-03-01/">
    <CORSRule>
        <AllowedOrigin>http(s)://YOUR_ADMIN_DOMAIN</AllowedOrigin>
        <AllowedMethod>POST</AllowedMethod>
        <AllowedMethod>PUT</AllowedMethod>
        <AllowedMethod>DELETE</AllowedMethod>
        <MaxAgeSeconds>3000</MaxAgeSeconds>
        <ExposeHeader>ETag</ExposeHeader>
        <AllowedHeader>*</AllowedHeader>
    </CORSRule>
</CORSConfiguration>
```

### CRUD Modules
#### CLI Generator
#### Migrations
#### Models
#### Controllers
#### Form Requests
#### Repositories
#### Listing view
#### Form view
##### input
##### textarea
##### rich textarea
##### select
##### multi select
##### date picker
##### medias
##### files
##### tags
##### publication state
##### slug
##### lang switcher
#### Customization

### Block editor
#### Blocks assets
#### Default blocks
#### Adding blocks

### Frontend controllers
#### SPF requests/responses helper
#### SEO helper
#### Exemples

### Roadmap
- [ ] Content versionning
- [ ] Preview changes without saving
- [ ] Auto saving
- [ ] Concurrent editing/locking
- [ ] Content review/approval worflow
- [ ] Media library improvments
- [ ] Admin assets compilation
- [ ] Generator improvments
- [ ] Block editor view helpers
- [ ] More form fields

### Other useful packages

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.
