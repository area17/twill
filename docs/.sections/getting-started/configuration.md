### Configuration

As mentioned above, Twill's default configuration allows you to get up and running quickly by providing environment variables.

Of couse, you can override any of Twill's provided configurations values from the empty `config/twill.php` file that was published in your app when you ran the `twill:install` command.

#### Global configuration

By default, Twill uses Laravel default application namespace `App`. You  can provide your own using the `namespace` configuration in your `config/twill.php` file:

```php
<?php

return [
    'namespace' => 'App',
];
```

You can also change the default variables that control where Twill's admin console is available:

```php
<?php

return [
    'admin_app_url' => env('ADMIN_APP_URL', 'admin.' . env('APP_URL')),
    'admin_app_path' => env('ADMIN_APP_PATH', ''),
];
```

If you have specific middleware needs, you can specify a custom middleware group for Twill's admin console routes:

```php
<?php

return [
    'admin_middleware_group' => 'web',
];
```

Twill registers its own exception handler in all controllers. If you need to customize it (to report errors on a 3rd party service like Sentry or Rollbar for example), you can opt-out from it in your `config/twill.php` file:

```php
<?php

return [
    'bind_exception_handler' => false,
];
```

And then extend it from your own `app/Exceptions/Handler.php` class:

```php
<?php

namespace App\Exceptions;

use A17\Twill\Exceptions\Handler as ExceptionHandler;
use Exception;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
...
```

Twill's users and their password resets are stored in `twill_users` and `twill_password_resets` tables respectively. If you started your application on Twill 1.1 or simply would like to provide custom tables names, use the following configuration options:

```php
<?php

return [
    'users_table' => 'twill_users',
    'password_resets_table' => 'twill_password_resets',
];
```

#### Enabled features

You can opt-in or opt-out from certain Twill features using the `enabled` array in your `config/twill.php` file. Values presented in the following code snippet are Twill's defaults:

```php
<?php

return [
    'enabled' => [
        'users-management' => true,
        'media-library' => true,
        'file-library' => true,
        'block-editor' => true,
        'buckets' => true,
        'users-image' => false,
        'settings' => true,
        'dashboard' => true,
        'search' => true,
        'users-description' => false,
        'activitylog' => true,
        'users-2fa' => false,
        'users-oauth' => false,
    ],
];
```

You do not need to override entire arrays of configuration options. For example, if you only want to disable Twill's dashboard, you do not need to include to entire `enabled` array to your own `config/twill.php` configuration file:

```php
<?php

return [
    'enabled' => [
        'dashboard' => false,
    ],
];
```

This is true to all following configuration arrays.

#### Media library

The `media_library` configuration array in `config/twill.php` allows you to provide Twill with your configuration for the media library disk, endpoint type and others options depending on your endpoint type. Most options can be controlled through environment variables, as you can see in the default configuration provided:

```php
<?php

return [
    'media_library' => [
        'disk' => 'twill_media_library',
        'endpoint_type' => env('MEDIA_LIBRARY_ENDPOINT_TYPE', 's3'),
        'cascade_delete' => env('MEDIA_LIBRARY_CASCADE_DELETE', false),
        'local_path' => env('MEDIA_LIBRARY_LOCAL_PATH', 'uploads'),
        'image_service' => env('MEDIA_LIBRARY_IMAGE_SERVICE', 'A17\Twill\Services\MediaLibrary\Imgix'),
        'acl' => env('MEDIA_LIBRARY_ACL', 'private'),
        'filesize_limit' => env('MEDIA_LIBRARY_FILESIZE_LIMIT', 50),
        'allowed_extensions' => ['svg', 'jpg', 'gif', 'png', 'jpeg'],
        'init_alt_text_from_filename' => true,
        'prefix_uuid_with_local_path' => config('twill.file_library.prefix_uuid_with_local_path', false),
        'translated_form_fields' => false,
    ],
];
```

Twill's media library supports the following endpoint types: `s3`, `azure` and `local`. 

**S3 endpoint**

By default, Twill uses the `s3` endpoint type to store your uploads on an AWS S3 bucket. To authorize uploads to S3, provide your application with the following environment variables:

```bash
S3_KEY=S3_KEY
S3_SECRET=S3_SECRET
S3_BUCKET=bucket-name
```

Optionally, you can use the `S3_REGION` variable to specify a region other than S3's default region (`us-east-1`).

When uploading images to S3, Twill sets the `acl` parameter to `private`. This is because images in your bucket should not be publicly accessible when using a service like [Imgix](https://imgix.com) on top of it. Only Imgix should have read-only access to your bucket, while your application obviously needs to have write access. If you intend to access images uploaded to S3 directly, set the `MEDIA_LIBRARY_ACL` variable or `acl` configuration option to `public-read`.

**Azure endpoint**

Twill supports `azure` endpoint type to store your uploads on an Microsoft Azure container.
 
To authorize uploads to Azure, provide your application with the following environment variables:

```bash
MEDIA_LIBRARY_ENDPOINT_TYPE=azure

AZURE_ACCOUNT_KEY=AZURE_ACCOUNT_KEY
AZURE_ACCOUNT_NAME=AZURE_ACCOUNT_NAME
AZURE_CONTAINER=AZURE_CONTAINER
```

**Local endpoint**

If you want your uploads to be stored on the server where your Laravel application is running, use the `local` endpoint type.  Define the `MEDIA_LIBRARY_LOCAL_PATH` environment variable or the `media_library.local_path` configuration option to provide Twill with your prefered upload path:

```bash
MEDIA_LIBRARY_ENDPOINT_TYPE=local
MEDIA_LIBRARY_LOCAL_PATH=uploads
```

To avoid running into `too large` errors when uploading to your server, you can choose to limit uploads through Twill using the `MEDIA_LIBRARY_FILESIZE_LIMIT` environment variable or `filesize_limit` configuration option. It is set to 50mb by default. Make sure to setup your PHP and webserver (apache, nginx, ....) to allow for the upload size specified here.
When using the `s3` endpoint type, uploads are not limited in size.

**Cascading uploads deletions**

By default, Twill will not delete images when deleting from Twill's media library UI, wether it is on S3 or locally.

You can decide to physically delete uploaded images using the `cascade_delete` option, which is also controlled through the `MEDIA_LIBRARY_CASCADE_DELETE` boolean environment variable:

```bash
MEDIA_LIBRARY_CASCADE_DELETE=false
```

**Allowed extensions**

The `allowed_extensions` configuration option is an array of file extensions that Twill's media library's uploader will accept. By default, `svg`, `jpg`, `gif`, `png` and `jpeg` extensions are allowed.

**Images rendering**

To render uploaded images, Twill's prefered service is [Imgix](https://imgix.com).

If you do not want or cannot use a third party service, or have very limited image rendering needs, Twill also provides a local image rendering service powered by [Glide](https://glide.thephpleague.com/). The following .env variables should get you up and running:

```bash
MEDIA_LIBRARY_ENDPOINT_TYPE=local
MEDIA_LIBRARY_IMAGE_SERVICE=A17\Twill\Services\MediaLibrary\Glide
```

#### Imgix

As noted above, by default, Twill uses and recommends using [Imgix](https://imgix.com) to transform, optimize, and intelligently cache your uploaded images. 

Specify your Imgix source url using the `IMGIX_SOURCE_HOST` environment variable or `source_host` configuration option.

```bash
IMGIX_SOURCE_HOST=source.imgix.net
```

By default, Twill will render Imgix urls with the `https` scheme. We do not see any reason why you would do so nowadays, but you can decide to opt-out using the `IMGIX_USE_HTTPS` environment variable or `use_https` configuration option.

Imgix offers the ability to use signed urls to prevent users from accessing images without parameters or different parameters than the ones you choose to use in your own application. You can enable that feature in Twill using the `IMGIX_USE_SIGNED_URLS` environment variable or `use_signed_urls` configuration option. If you enable signed urls, Imgix provides you with a signature key. Provide it to Twill using the `IMGIX_SIGN_KEY` environment variable. 

```bash
IMGIX_USE_SIGNED_URLS=true
IMGIX_SIGN_KEY=xxxxxxxxxxxxxxxx
```

:::danger
You should never store any sort of credentials in source control (eg. Git). 

That's exactly why in the case of the Imgix signature key, we do not say that you could provide it to Twill using the sign_key configuration option of the imgix configuration array.

Always use environment variables for credentials.
:::

Finally, Twill's default Imgix configuration includes 4 different image transformation parameter sets that are used by helpers you will find in the [media library's documentation](#media-library-3):

- `default_params`: used by all image url functions in `A17\Twill\Services\MediaLibrary\Imgix` but overrided by the following parameter sets
- `lqip_default_params`: used by the Low Quality Image Placeholder url function
- `social_default_params`: used by the social image url function (for Facebook, Twitter, ... shares)
- `cms_default_params`: used by the CMS image url function. This only affects rendering of images in Twill's admin console (eg. in the media library and image fields).

See [Imgix's API reference](https://docs.imgix.com/apis/url) for more information about those parameters.

```php
<?php

return [
    'imgix' => [
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
        'source_host' => env('IMGIX_SOURCE_HOST'),
        'use_https' => env('IMGIX_USE_HTTPS', true),
        'use_signed_urls' => env('IMGIX_USE_SIGNED_URLS', false),
        'sign_key' => env('IMGIX_SIGN_KEY'),
    ],
];
```

#### File library

The `file_library` configuration array in `config/twill.php` allows you to provide Twill with your configuration for the file library disk, endpoint type and other options depending on your endpoint type. Most options can be controlled through environment variables, as you can see in the default configuration provided:

```php
<?php

return [
    'file_library' => [
        'disk' => 'twill_file_library',
        'endpoint_type' => env('FILE_LIBRARY_ENDPOINT_TYPE', 's3'),
        'cascade_delete' => env('FILE_LIBRARY_CASCADE_DELETE', false),
        'local_path' => env('FILE_LIBRARY_LOCAL_PATH', 'uploads'),
        'file_service' => env('FILE_LIBRARY_FILE_SERVICE', 'A17\Twill\Services\FileLibrary\Disk'),
        'acl' => env('FILE_LIBRARY_ACL', 'public-read'),
        'filesize_limit' => env('FILE_LIBRARY_FILESIZE_LIMIT', 50),
        'allowed_extensions' => [],
        'prefix_uuid_with_local_path' => false,
    ],
];
```

Twill's file library supports the following endpoint types: `s3` and `local`. 

**S3 endpoint**

By default, Twill uses the `s3` endpoint type to store your uploads on an AWS S3 bucket. To authorize uploads to S3, provide your application with the following environment variables:

```bash
S3_KEY=S3_KEY
S3_SECRET=S3_SECRET
S3_BUCKET=bucket-name
```

Optionally, you can use the `S3_REGION` variable to specify a region other than S3's default region (`us-east-1`).

When uploading files to S3, Twill sets the `acl` parameter to `public-read`. This is because Twill's default file service produces direct S3 urls. If you do not intend to access files uploaded to S3 directly, set the `FILE_LIBRARY_ACL` variable or `acl` configuration option to `public-read`.

**Local endpoint**

If you want your uploads to be stored on the server where your Laravel application is running, use the `local` endpoint type.  Define the `FILE_LIBRARY_LOCAL_PATH` environment variable or the `file_library.local_path` configuration option to provide Twill with your prefered upload path. Always include a trailing slash like in the following example:

```bash
FILE_LIBRARY_ENDPOINT_TYPE=local
FILE_LIBRARY_LOCAL_PATH=uploads/
```

To avoid running into `too large` errors when uploading to your server, you can choose to limit uploads through Twill using the `FILE_LIBRARY_FILESIZE_LIMIT` environment variable or `filesize_limit` configuration option. It is set to 50mb by default. Make sure to setup your PHP and webserver (apache, nginx, ....) to allow for the upload size specified here.
When using the `s3` endpoint type, uploads are not limited in size.

**Cascading uploads deletions**

By default, Twill will not delete files when deleting from Twill's file library's UI, wether it is on S3 or locally.

You can decide to physically delete uploaded files using the `cascade_delete` option, which is also controlled through the `FILE_LIBRARY_CASCADE_DELETE` boolean environment variable:

```bash
FILE_LIBRARY_CASCADE_DELETE=false
```

**Files url service**

Twill's provided service for files creates direct urls to the disk they were uploaded to (ie. S3 urls or urls on your domain depending on your endpoint type). You can change the default service using the `FILE_LIBRARY_IMAGE_SERVICE` environment variable or the `file_library.image_service` configuration option.

See the [file library's documentation](#file-library-2) for more information.


**Allowed extensions**

The `allowed_extensions` configuration option is an array of file extensions that Twill's file library uploader will accept. By default, it is empty, all extensions are allowed.

#### Debug

The [Laravel Debug Bar](https://github.com/barryvdh/laravel-debugbar) and [Inspector](https://github.com/lsrur/inspector) packages are installed and registered by Twill, except on production environments.

On `development`, `local` and `staging` environment, Debug Bar is enabled by default. You can use Inspector instead by using the `DEBUG_USE_INSPECTOR` environment variable.

If you do not want to see the Debug Bar on the frontend of your Laravel application but want to keep it in Twill's admin console while developing or on staging servers, use the `DEBUG_BAR_IN_FE` environment variable:

```bash
DEBUG_BAR_IN_FE=false
```

And add the `noDebugBar` to your frontend route group middlewares. 
Example in a default Laravel 5.7 application's `RouteServiceProvider`:

```php
Route::middleware('web', 'noDebugBar')
    ->namespace($this->namespace)
    ->group(base_path('routes/web.php'));
```

### Navigation

The `config/twill-navigation.php` file manages the navigation of your custom admin console. Using Twill's UI, the package provides 3 levels of navigation: global, primary and secondary. This file simply contains a nested array description of your navigation.

Each entry is defined by multiple options.
The simplest entry has a `title` and a `route` option which is a Laravel route name. A global entry can define a `primary_navigation` array that will contain more entries. A primary entry can define a `secondary_navigation` array that will contain even more entries.

Two other options are provided that are really useful in conjunction with the CRUD modules you'll create in your application: `module` and `can`. `module` is a boolean to indicate if the entry is routing to a module route. By default it will link to the index route of the module you used as your entry key. `can` allows you to display/hide navigation links depending on the current user and permission name you specify.

Example:

```php
<?php

return [
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
];
```

To make it work properly and to get active states automatically in Twill's UI, you should structure your routes in the same way like the example here:

```php
<?php

Route::group(['prefix' => 'work'], function () {
    Route::module('projects');
    Route::module('clients');
    Route::module('industries');
    Route::module('studios');
});
```
