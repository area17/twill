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

This package is in very early stage as of December '16. Most of the code it provides has been battle tested over the last 2 years through thousands of Unfuddle tickets but you know how it is... I'm pretty sure most of the bugs you'll encounter will be related to the creation of this package and how it hooks into your application. Feel free to ping on HipChat anytime :)

## Table of content

* [Introduction](#introduction)
* [Install](#install)
* [Usage](#usage)
 * [Static templates](#static-templates)
 * [Configuration](#configuration)
 * [Users management](#users-management)
 * [CRUD Modules](#crud-modules)
 * [Media Library](#media-library)
 * [File library](#file-library)
 * [S3 direct upload](#s3-direct-upload)
 * [Block editor](#block-editor)
 * [Frontend controllers](#frontend-controllers)
 * [Roadmap](#roadmap)
 * [Other useful packages](#other-useful-packages)
* [Changelog](#changelog)

## Install

Via Composer

```bash
composer require a17/laravel-cms-toolkit
```
Add the CMS Toolkit service provider in `config/app.php`:

```php
<?php

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

This file manages the navigation of your admin area. Using the CMS UI Toolkit, the package provides 3 levels of navigation: global, primary and secondary. This file simply contains a nested array description of your navigation. 

Each entry is defined by multiple options.
The simplest entry has a `title` and a `route` option which is a Laravel route name. A global entry can define a `primary_navigation` array that will contains more entries. Same thing for the `primary_navigation` entries but with `secondary_navigation`. 

Two other options are provided that are really useful in conjunction with the CRUD modules you'll create in your application: `module` and `can`. `module` is a boolean to indicate if the entry is routing to a module route. By default it will link to the index route of the module you used as your entry key. `can` allows you to display/hide navigation links depending on the current user and permission name you specify.

Example:

```php
<?php

return [
    'dashboard' => [
        'title' => 'Dashboard',
        'route' => 'admin.dashboard',
    ],
    'work' => [
        'title' => 'Work',
        'route' => 'admin.work.projects.index',
        'primary_navigation' => [
            'projects' => [
                'title' => 'Projects',
                'module' => true,
            ],
            'clients' => [
                'title' => 'Clients',
                'module' => true,
            ],
            'industries' => [
                'title' => 'Industries',
                'module' => true,
            ],
            'studios' => [
                'title' => 'Studios',
                'module' => true,
            ],
        ],
    ],
    'users' => [
        'can' => 'list',
        'title' => 'Users',
        'module' => true,
    ],
];
```

To make it work properly and to get active states automatically, you should structure your routes in the same way using for example here: 

```php
<?php

Route::get('/dashboard')->...->name('admin.dashboard');
Route::group(['prefix' => 'work'], function ({
    Route::module('projects');
    Route::module('clients');
    Route::module('industries');
    Route::module('studios');
});
```

### Users management

Authentication and authorization are provided by default in Laravel. This package simply leverages it and configure the views with the A17 CMS UI Toolkit for you. By default, users can login at `/login` and also reset their password through that screen. New users have to start by resetting their password before initial access to the admin application. You should redirect users to anywhere you want in your application after they login. The cms-toolkit configuration file has an option for you to change the default redirect path (`auth.login_redirect_path`).

#### Roles
The package currently only provides 3 different roles:
- view only
- publisher
- admin

#### Permissions
View only users are able to:
- login
- view CRUD listings
- filter CRUD listings
- view media/file library
- download original files from the media/file library
- edit their own profile

Publishers have the same permissions as view only users plus:
- full CRUD permissions
- publish
- sort
- upload new images/files to the media/file library

Admin user have the same permissions as publisher users plus:
- full permissions on users

There is also a super admin user that can impersonate other users at `/users/impersonate/{id}`. This can be really useful for you to test your features with different user roles without having to logout/login manually. Also when debugging a ticket reported by a specific user. Stop impersonating by going to `/users/impersonate/stop`.


#### Extending user roles and permissions
You can create new permissions on the existing roles by using the Gate façade in your `AuthServiceProvider`. The new can middleware Laravel provides by default is very easy to use, either through route definition or controller constructor.

You should follow the Laravel documentation regarding [authorization](https://laravel.com/docs/5.3/authorization). It's pretty good. Also if you would like to bring administration of roles and permissions to the admin application, [spatie/laravel-permission](https://github.com/spatie/laravel-permission) would probably be your best friend. The Opera CMS had that feature but it was not very well developed which makes it a pain to use.

### CRUD Modules
#### CLI Generator
You can generate all the files needed for a new CRUD using the generator:

```bash
php artisan cms-toolkit:module YourPluralModuleName
```

It will generate a migration file, a model, a repository, a controller, a form request object and an index and form views.

#### Migrations
Migrations are regular Laravel migrations. A few helpers are available to create the default fields any CRUD module will use:

```php
<?php

Schema::create('table_name_plural', function (Blueprint $table) {
    createDefaultTableFields($tableName)
    // will add the following inscructions to your migration file
    // $table->increments('id');
    // $table->softDeletes();
    // $table->timestamps();
});

Schema::create('table_name_singular_translations', function (Blueprint $table) {
    createDefaultTranslationsTableFields($table, $tableNameSingular)
    // will add the following inscructions to your migration file
    // createDefaultTableFields($table);
    // $table->string('locale', 6)->index();
    // $table->boolean('active');
    // $table->integer("$tableNameSingular_id")->unsigned();
    // $table->foreign("$tableNameSingular_id", "fk_$tableNameSingular_translations_$tableNameSingular_id")->references('id')->on($table)->onDelete('CASCADE');
    // $table->unique(["$tableNameSingular_id", 'locale']);
});

Schema::create('table_name_singular_slugs', function (Blueprint $table) {
    createDefaultSlugsTableFields($table, $tableNameSingular)
    // createDefaultTableFields($table);        // will add the following inscructions to your migration file
    // $table->string('slug');
    // $table->string('locale', 6)->index();
    // $table->boolean('active');
    // $table->integer("$tableNameSingular_id")->unsigned();
    // $table->foreign("$tableNameSingular_id", "fk_$tableNameSingular_translations_$tableNameSingular_id")->references('id')->on($table)->onDelete('CASCADE')->onUpdate('NO ACTION');
});
```

A few CRUD controllers require that your model to have a field in the database with a specific name: `published` and `position`, so stick with those column names if you are going to use publication status and sortable listings. When using the block editor, you can name the field that will contains the blocks json whatever you want but it's type should be `json`.


#### Models

Set your fillables to prevent mass-assignement. Very important as we use `request()->all()` in the module controller.


For fields that should always be saved as null in the database when not sent by the form, use the `nullable` array.

For fields that should always be saved to false in the database when not sent by the form, use the `checkboxes` array.

Depending on the features you need on your model, include the availables traits and configure their respective options:

- Sorts: implement the `A17\CmsToolkit\Models\Behaviors\Sortable` interface and add a position field to your fillables.

- HasTranslation: add translated fields in the `translatedAttributes` array and in the `fillable` array of the generated translatable model in `App/Models/Translations` (always keep the active and locale fields).

- HasMedias: add the `mediasParams` configuration array:

```php
<?php

public $mediasParams = [
    'hero' => [ // role name
        'default' => '16/9', //crop name => ratio as a fraction or number
        'square' => '1',
    ],
    'logo' => [
        'default' => 0, // no crop
    ],
    'profile' => [
        'desktop' => '1340/560',
        'tablet' => '780/395',
        'mobile' => '320/270',
    ],

];
```

- HasFiles: add the `filesParams` configuration array

```php
<?php

public $filesParams = ['finishe', 'caring', 'warranty']; // a list of file roles
```


- HasSlug: specify the field(s) that is going to be used to create the slug in the `slugAttributes` array and create a Slug model in the `App/Models/Slugs` directory with the following content:

```php
<?php

namespace App\Models\Slugs;

use A17\CmsToolkit\Models\Model;

class ModelSlug extends Model
{

    protected $table = 'model_slugs';

}

```

#### Repositories
Nothing to do by default after generation.

Depending on the model feature, include one or multiple of those traits: `HandleTranslations`, `HandleSlugs`, `HandleMedias`, `HandleFiles`.

Repositories allows you to modify the default behavior of your models by providing some entry points in the form of methods that you might implement:

- for filtering:

```php
<?php

// implement the filter method
public function filter($query, array $scopes = []) {

    // and use the following helpers
    
    // add a where like clause
    $this->addLikeFilterScope($query, $scopes, 'field_in_scope');
    
    // add orWhereHas clauses
    $this->searchIn($query, $scopes, 'field_in_scope', ['field1', 'field2', 'field3']);
    
    // add a whereHas clause
    $this->addRelationFilterScope($query, $scopes, 'field_in_scope', 'relationName');
    
    // or just go manually with the $query object
    if (isset($scopes['field_in_scope'])) {
      $query->orWhereHas('relationName', function ($query) use ($scopes) {
          $query->where('field', 'like', '%' . $scopes['field_in_scope'] . '%');
      });
    }
    
    // don't forget to call the parent filter function
    return parent::filter($query, $scopes);
}
```

- for custom ordering:

```php
<?php

// implement the order method
public function order($query, array $orders = []) {
    // don't forget to call the parent order function
    return parent::order($query, $orders);
}
```

- for custom form fieds

```php
<?php

// implement the getFormFields method
public function getFormFields($object) {
    // don't forget to call the parent getFormFields function
    $fields = parent::getFormFields($object);
    
    // get oneToMany relationship for select multiple input
    $fields = $this->getFormFieldsForMultiSelect($fields, 'relationName');
    
    // return fields
    return $fields
}

```

- for custom field preparation before create action


```php
<?php

// implement the prepareFieldsBeforeCreate method
public function prepareFieldsBeforeCreate($fields) {
    // don't forget to call the parent prepareFieldsBeforeCreate function
    return parent::prepareFieldsBeforeCreate($fields);
}

```

- for custom field preparation before save action


```php
<?php

// implement the prepareFieldsBeforeSave method
public function prepareFieldsBeforeSave($object, $fields) {
    // don't forget to call the parent prepareFieldsBeforeSave function
    return parent:: prepareFieldsBeforeSave($object, $fields);
}

```

- for after save actions (like attaching a relationship)

```php
<?php

// implement the afterSave method
public function afterSave($object, $fields) {
    // for exemple, to sync a many to many relationship
    // $object->relationName()->sync($fields['relationName'] ?? []);
    parent::afterSave($object, $fields);}

```

#### Controllers
Nothing to do by default after generation.

TODO more docs here on filters, form inputs data, default ordering, per page settings.

If your model is sortable set `perPage` to -1 to prevent pagination.

#### Form Requests
Classic Laravel 5 [form request validation](https://laravel.com/docs/5.3/validation#form-request-validation).

There is an helper to define rules for translated fields without having to deal with each locales:

```php
<?php

$this->rulesForTranslatedFields([
 // regular rules
], [
  // translated fields rules with just the field name like regular rules
]);
```

There is also an helper to define validation messages for translated fields:

```php
<?php

$this->messagesForTranslatedFields([
 // regular messages
], [
  // translated fields messages
]);
```

#### Listing view

```php
@extends('cms-toolkit::layouts.resources.index', [
    'create' => true, // enable/disable the create action
    'edit' => true, // enable/disable the edit action
    'delete' => true, // enable/disable the delete action
    'sort' => false, // enable/disable the sort action
    'search' => true, // enable/disable the search field
    'publish' => true, // enable/disable the publish action
    'title' => 'defaults to module name',
    'columns' => [
        'image' => [ 
            'title' => 'Image',
            'thumb' => true, // image column
            'variant' => [
                'role' => 'roleName',
                'crop' => 'cropName',
            ],
        ],
        'fieldName' => [ // field column
            'title' => 'Field title',
            'edit_link' => true, // column content is wrapped in a link to the edit action
            'sort' => true, // column is sortable
            'field' => 'fieldName', // column field
            'col' => 1 // column display width
        ],
        'relationName' => [ // relation column
            'title' => 'Relation name',
            'sort' => true,
            'sortField' => 'foreign_key',
            'relationship' => 'relationName',
            'field' => 'relationFieldToDisplay'
        ],
        'presenterMethodField' => [ // presenter column
            'title' => 'Field title',
            'field' => 'presenterMethod',
            'present' => true,
        ]
    ]
])
```

You can override each components of the listing :
- _column.blade.php
- _column_with_edit_link.blade.php
- _create_action.blade.php
- _delete_action.blade.php
- _edit_action.blade.php
- _paginator.blade.php
- _publish_action.blade.php
- _sort_action.blade.php
- _sort_link.blade.php

Or the whole view in admin/layouts/resources.

You can also add columns after the publication state and before the edit action by creating `_before_index_headers.blade.php/_after_index_header.blade.php` and `_before_index_columns.blade.php/_after_index_columns.blade.php` in your admin module views directory.

#### Form view

##### input (and input_locale)

```php
@formField('input', ['field' => 'name', 'field_name' => 'Name'])
```

##### textarea (and textarea_locale)

```php
@formField('textarea', [
    'field' => 'name',
    'field_name' => 'Name',
    'textLimit' => 100,
    'required' => 'required'
])
```

##### rich textarea (and rich_textarea_locale)

```php
@include('admin.layouts.form_partials._rich_textarea', [
    'field' => 'name',
    'field_name' => 'Name',
    'data_medium_editor_options' => "editorOptions", // optional
    'hint' => 'Hint',
    'textLimit' => 100,
    'required' => 'required'

])

<script>
       var editorOptions = {
            toolbar : {
                buttons: ['bold', 'italic',  'unorderedlist', 'orderedlist']
            }
        };
</script>
```

##### select

```php
@formField('select', [
    'field' => "relationship_id",
    'field_name' => "Relationship",
    'list' => $relationshipList,
    'data_behavior' => 'selector',
    'placeholder' => 'Select a relationship'
])
```

##### multi select

```php
@formField('multi_select', [
    'field' => "relationship",
    'field_name' => 'Related relationship',
    'list' => $relationshipList,
    'placeholder' => 'Add some relationship',
    'maximumSelectionLength' => 5
])

```

##### date picker

```php
@formField('date_picker', [
    'fieldname' => "Date",
    'field' => "date",
])

```

##### medias

```php
@ formField('medias', [
    'media_role' => 'media_role', 
    'media_role_name' => 'Media role name',
    'with_multiple' => true/false, 
    'max' => 5, 
    'no_crop' => true/false
])
```

##### files

```php
@ formField('files', [
    'file_role' => 'media_role', 
    'file_role_name' => 'Media role name',
    'with_multiple' => true/false, 
    'max' => 5, 
])
```

##### publication state

```php
@formField('publish_status')
@formField('optional_languages') // only activate the default locale
@formField('all_languages') // activate all languages
```

##### slug

```php
@formField('slug_input', [
    'currentSlug' => isset($item) ? $item->getActiveSlug() : '',
    'currentName' => isset($item) ? $item->name : '',
])
```

##### lang switcher

```php
@formField('lang_switcher')
```

### Media Library
#### Storage provider
The media and files libraries currently support S3 and local storage. Head over to the `cms-toolkit` configuration file to setup your storage disk and configurations. Also check out the S3 direct upload section of this documentation to setup your IAM users and bucket if you want to use S3 as a storage provider.

#### Image Rendering Service
This package currently ship with only one rendering service, [Imgix](https://www.imgix.com/). It is very simple to implement another one like [Cloudinary](http://cloudinary.com/) or even a local service like [Glide](http://glide.thephpleague.com/) or [Croppa](https://github.com/BKWLD/croppa).
You would have to implement the `ImageServiceInterface` and modify your `cms-toolkit` configuration value `media_library.image_service` with your implementation class.
Here are the methods you would have to implement:

```php
<?php

public function getUrl($id, array $params = []);
public function getLQIPUrl($id, array $params = []);
public function getSocialUrl($id, array $params = []);
public function getCmsUrl($id, array $params = []);
public function getRawUrl($id);
public function getDimensions($id);
public function getSocialFallbackUrl();
public function getTransparentFallbackUrl();
```

#### Role & Crop params
Each of the data models in your application can have different images roles and crop. 

For exemple, roles for a People model could be `profile` and `cover`. This allow you display different images for your data modal in the design, depending on the current screen. 

Crops are complementary or can be used on their own with a single role to define multiple cropping ratios on the same image. 

For example, your Person `cover` image could have a `square` crop for mobile screen, but could use a `16/9` crop on larger screen. Those values are editable at your convenience for each model, even if there are already some crop created in the CMS. 

The only thing you have to do to make it work is to compose your model and repository with the appropriate traits, respectively `HasMedias` and `HandleMedias`, setup your `$mediaParams` configuration and use the `medias` form partial in your form view (more info in the CRUD section).

When it comes to using those data model images in the frontend site, there are a few methods on the `HasMedias` trait that will help you to retrieve them for each of your layouts:

```php
<?php

/**
 * Returns the url of the associated image for $roleName and $cropName.
 * Optionally add params compatible with the current image service in use like w or h.
 * Optionally indicate that you can provide a fallback so that this method will return null
 * instead of the fallback image.
 * Optionally indicate that you are displaying this image in the CMS views.
 * Optionally provide a $media object if you already retrieved one to prevent more SQL requests.
 */
$model->image($roleName, $cropName[, array $params, $has_fallback, $cms, $media])

/**
 * Returns an array of images URLs assiociated with $roleName and $cropName with appended $params.
 * Use this in conjunction with a media form field with the with_multiple and max option.
 */
$model->images($roleName, $cropName[, array $params])

/**
 * Returns the image for $roleName and $cropName with default social image params and $params appended
 */
$model->socialImage($roleName, $cropName[, array $params, $has_fallback])

/**
 * Returns the lqip base64 encoded string from the database for $roleName and $cropName.
 * Use this in conjunction with the RefreshLQIP Artisan command.
 */
$model->lowQualityImagePlaceholder($roleName, $cropName[, array $params, $has_fallback])

/**
 * Returns the image for $roleName and $cropName with default CMS image params and $params appended.
 */
$model->cmsImage($roleName, $cropName[, array $params, $has_fallback])

/**
 * Returns the alt text of the image associated with $roleName.
 */
$model->imageAltText($roleName)
 
/**
 * Returns the caption of the image associated with $roleName.
 */
$model->imageCaption($roleName)

/**
 * Returns the background position setting associated with $roleName and $cropName.
 * Use this in conjunction with a media form field with the with_background_position option.
 */
$model->imageBackgroundPosition($roleName, $cropName)

/**
 * Returns the image object associated with $roleName.
 */
$model->imageObject($roleName)
```


### File library
The file library is much simpler but also work with S3 and local storage. To associate files to your model, use the `HasFiles` and `HandleFiles` traits, the `$filesParams` configuration and the `files` form partial.

When it comes to using those data model files in the frontend site, there are a few methods on the `HasFiles` trait that will help you to retrieve direct URLs:

```php
<?php

/**
 * Returns the url of the associated file for $roleName.
 * Optionally indicate which locale of the file if your site has multiple languages.
 * Optionally provide a $file object if you already retrieved one to prevent more SQL requests.
 */
$model->file($roleName[, $locale, $file])

/**
 * Returns an array of files URLs assiociated with $roleName.
 * Use this in conjunction with a files form field with the with_multiple and max option.
 */
$model->filesList($roleName[, $locale])

/**
 * Returns the file object associated with $roleName.
 */
$model->fileObject($roleName)
```

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

### Block editor
#### Blocks assets
TODO
#### Default blocks
TODO
#### Adding blocks
TODO

### Frontend controllers
#### SPF requests/responses helper
TODO
#### SEO helper
TODO
#### Examples
TODO

### Roadmap
- [ ] Content versionning
- [ ] Preview changes without saving
- [ ] Auto saving
- [ ] Concurrent editing/locking
- [ ] Content review/approval worflow
- [ ] Media library improvments
- [ ] Admin assets compilation
- [ ] Generator improvments
- [ ] Block editor view helpers
- [ ] More form fields

### Other useful packages

- [laravel/scout](https://laravel.com/docs/5.3/scout) provide full text search on your Eloquent models.
- [laravel/passport](https://laravel.com/docs/5.3/passport) makes API authentication a breeze.
- [spatie/laravel-fractal](https://github.com/spatie/laravel-fractal) is a nice and easy integration with [Fractal](http://fractal.thephpleague.com) to create APIs.
- [laravel/socialite](https://github.com/laravel/socialite) provides an expressive, fluent interface to OAuth authentication.
- [spatie/laravel-responsecache](https://github.com/spatie/laravel-responsecache) can speed up your app by caching the entire response.
- [spatie/laravel-backup](https://github.com/spatie/laravel-backup) creates a backup of your application. The backup is a zipfile that contains all files in the directories you specify along with a dump of your database.
- [jenssegers/rollbar](https://github.com/jenssegers/laravel-rollbar) adds a listener to Laravel's logging component to work with Rollbar.
- [sentry/sentry-laravel](https://github.com/getsentry/sentry-laravel) is a Laravel integration for Sentry.
- [arcanedev/log-viewer](https://github.com/ARCANEDEV/LogViewer) allows you to manage and keep track of each one of your logs files in a nice web UI.
- [roumen/sitemap](https://github.com/RoumenDamianoff/laravel-sitemap) is a very complelete sitemap generator.
- [flynsarmy/csv-seeder](https://github.com/Flynsarmy/laravel-csv-seeder) allows CSV based database seeds.
- [ufirst/lang-import-export](https://github.com/ufirstgroup/laravel-lang-import-export)  provides artisan commands to import and export language files from and to CSV
- [nikaia/translation-sheet](https://github.com/nikaia/translation-sheet) allows translating Laravel languages files using a Google Spreadsheet.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.
