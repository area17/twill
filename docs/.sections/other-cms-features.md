## Dashboard

Once you have created and configured multiple CRUD modules in your Twill's admin console, you can configure Twill's dashboard in `config/twill.php`.

For each module that you want to enable in a part or all parts of the dashboad, add an entry to the `dashboard.modules` array, like in the following example:

```php
return [
    'dashboard' => [
        'modules' => [
            'projects' => [ // module name if you added a morph map entry for it, otherwise FQCN of the model (eg. App\Models\Project)
                'name' => 'projects', // module name
                'label' => 'projects', // optional, if the name of your module above does not work as a label
                'label_singular' => 'project', // optional, if the automated singular version of your name/label above does not work as a label
                'routePrefix' => 'work', // optional, if the module is living under a specific routes group
                'count' => true, // show total count with link to index of this module
                'create' => true, // show link in create new dropdown
                'activity' => true, // show activities on this module in actities list
                'draft' => true, // show drafts of this module for current user 
                'search' => true, // show results for this module in global search
            ],
            ...
        ],
        ...
    ],
    ...
];
```

You can also enable a Google Analytics module:

```php
return [
    'dashboard' => [
        ...,
        'analytics' => [
            'enabled' => true,
            'service_account_credentials_json' => storage_path('app/analytics/service-account-credentials.json'),
        ],
    ],
    ...
];
```

It is using Spatie's [Laravel Analytics](https://github.com/spatie/laravel-analytics) package.

Follow [Spatie's documentation](https://github.com/spatie/laravel-analytics#how-to-obtain-the-credentials-to-communicate-with-google-analytics) to setup a Google service account and download a json file containing your credentials, and provide your Analytics view ID using the `ANALYTICS_VIEW_ID` environment variable.

## Global search

By default, Twill's global search input is always available in the dashboard and behind the top-right search icon on other Twill's screens. By default, the search input performs a LIKE query on the title attribute only. If you like, you can specify a custom list of attributes to search for in each dashboard enabled module:

```php
return [
    'dashboard' => [
        'modules' => [
            'projects' => [
                'name' => 'projects',
                'routePrefix' => 'work',
                'count' => true,
                'create' => true,
                'activity' => true,
                'draft' => true,
                'search' => true,
                'search_fields' => ['name', 'description']
            ],
            ...
        ],
        ...
    ],
    ...
];
```

You can also customize the endpoint to handle search queries yourself:

```php
return [
    'dashboard' => [
        ...,
        'search_endpoint' => 'your.custom.search.endpoint.route.name',
    ],
    ...
];
```

You will need to return a collection of values, like in the following example:

```php
return $searchResults->map(function ($item) use ($module) {
    try {
        $author = $item->revisions()->latest()->first()->user->name ?? 'Admin';
    } catch (\Exception $e) {
        $author = 'Admin';
    }

    return [
        'id' => $item->id,
        'href' => moduleRoute($moduleName['name'], $moduleName['routePrefix'], 'edit', $item->id),
        'thumbnail' => $item->defaultCmsImage(['w' => 100, 'h' => 100]),
        'published' => $item->published,
        'activity' => 'Last edited',
        'date' => $item->updated_at->toIso8601String(),
        'title' => $item->title,
        'author' => $author,
        'type' => str_singular($module['name']),
    ];
})->values();

```

## Featuring content
Twill's buckets allow you to provide publishers with featured content management screens. You can add multiple pages of buckets anywhere you'd like in your CMS navigation and, in each page, multiple buckets with different rules and accepted modules. In the following example, we will assume that our application has a Guide model and that we want to feature guides on the homepage of our site. Our site's homepage has multiple zones for featured guides: a primary zone, that shows only one featured guide, and a secondary zone, that shows guides in a carousel of maximum 10 items.

First, you will need to enable the buckets feature. In `config/twill.php`:
```php
'enabled' => [
    'buckets' => true,
],
```

Then, define your buckets configuration:

```php
'buckets' => [
    'homepage' => [
        'name' => 'Home',
        'buckets' => [
            'home_primary_feature' => [
                'name' => 'Home primary feature',
                'bucketables' => [
                    [
                        'module' => 'guides',
                        'name' => 'Guides',
                        'scopes' => ['published' => true],
                    ],
                ],
                'max_items' => 1,
            ],
            'home_secondary_features' => [
                'name' => 'Home secondary features',
                'bucketables' => [
                    [
                        'module' => 'guides',
                        'name' => 'Guides',
                        'scopes' => ['published' => true],
                    ],
                ],
                'max_items' => 10,
            ],
        ],
    ],
],
```

You can allow mixing modules in a single bucket by adding more modules to the `bucketables` array.
Each `bucketable` should have its [model morph map](https://laravel.com/docs/5.5/eloquent-relationships#polymorphic-relations) defined because features are stored in a polymorphic table.
In your AppServiceProvider, you can do it like the following:

```php
use Illuminate\Database\Eloquent\Relations\Relation;
...
public function boot()
{
    Relation::morphMap([
        'guides' => 'App\Models\Guide',
    ]);
}
```

Finally, add a link to your buckets page in your CMS navigation:

```php
return [
   'featured' => [
       'title' => 'Features',
       'route' => 'admin.featured.homepage',
       'primary_navigation' => [
           'homepage' => [
               'title' => 'Homepage',
               'route' => 'admin.featured.homepage',
           ],
       ],
   ],
   ...
];
```

By default, the buckets page (in our example, only homepage) will live under the /featured prefix.
But you might need to split your buckets page between sections of your CMS. For example if you want to have the homepage bucket page of our example under the /pages prefix in your navigation, you can use another configuration property:

```php
'bucketsRoutes' => [
    'homepage' => 'pages'
]
```

## Settings sections
Settings sections are standalone forms that you can add to your Twill's navigation to give publishers the ability to manage simple key/value records for you to then use anywhere in your application codebase.

Start by enabling the `settings` feature in your `config/twill.php` configuration file `enabled` array. See [Twill's configuration documentation](#enabled-features) for more information.

If you did not enable this feature before running the `twill:install` command, you need to copy the migration in `vendor/area17/twill/migrations/create_settings_table.php` to your own `database/migrations` directory and migrate your database before continuing.

To create a new settings section, add a blade file to your `resources/views/admin/settings` folder. The name of this file is the name of your new settings section.

In this file, you can use `@formField('input')` Blade directives to add new settings. The name attribute of each form field is the name of a setting. Wrap them like in the following example:

```php
@extends('twill::layouts.settings')

@section('contentFields')
    @formField('input', [
        'label' => 'Site title',
        'name' => 'site_title',
        'textLimit' => '80'
    ])
@stop
```

If your `translatable.locales` configuration array contains multiple language codes, you can enable the `translated` option on your settings input form fields to make them translatable.

At this point, you want to add an entry in your `config/twill-navigation.php` configuration file to show the settings section link:

```php
return [
    ...
    'settings' => [
        'title' => 'Settings',
        'route' => 'admin.settings',
        'params' => ['section' => 'section_name'],
        'primary_navigation' => [
            'section_name' => [
                'title' => 'Section name',
                'route' => 'admin.settings',
                'params' => ['section' => 'section_name']
            ],
            ...
        ]
    ],
];
```

Each Blade file you create in `resources/views/admin/settings` creates a new section available for you to add in the `primary_navigation` array of your `config/twill-navigation.php` file.

You can then retrieve the value of a specific setting by its key, which is the name of the form field you defined in your settings form, either by directly using the `A17\Twill\Models\Setting` Eloquent model or by using the provided `byKey` helper in `A17\Twill\Repositories\SettingRepository`:

```php
<?php

use A17\Twill\Repositories\SettingRepository;
...

app(SettingRepository::class)->byKey('site_title');
app(SettingRepository::class)->byKey('site_title', 'section_name');
```

## User management
Authentication and authorization are provided by default in Laravel. This package simply leverages what Laravel provides and configures the views for you. By default, users can login at `/login` and can also reset their password through that same screen. New users have to reset their password before they can gain access to the admin application. By using the twill configuration file, you can change the default redirect path (`auth_login_redirect_path`) and send users to anywhere in your application following login.

#### Roles
The package currently provides three different roles:
- view only
- publisher
- admin

#### Permissions
Default permissions are as follows. To learn how permissions can be modified or extended, see the next section.

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
- feature
- upload new images/files to the media/file library

Admin users have the same permissions as publisher users plus:
- full permissions on users

There is also a super admin user that can impersonate other users at `/users/impersonate/{id}`. The super admin can be a useful tool for testing features with different user roles without having to logout/login manually, as well as for debugging issues reported by specific users. You can stop impersonating by going to `/users/impersonate/stop`.

#### Extending user roles and permissions
You can create or modify new permissions for existing roles by using the Gate façade in your `AuthServiceProvider`. The `can` middleware, provided by default in Laravel, is very easy to use, either through route definition or controller constructor.

To create new user roles, you could extend the default enum UserRole by overriding it using Composer autoloading. In `composer.json`:

```json
    "autoload": {
        "classmap": [
            "database/seeds",
            "database/factories"
        ],
        "psr-4": {
            "App\\": "app/"
        },
        "files": ["app/Models/Enums/UserRole.php"],
        "exclude-from-classmap": ["vendor/area17/twill/src/Models/Enums/UserRole.php"]
    }
```

In `app/Models/Enums/UserRole.php` (or anywhere else you'd like actually, only the namespace needs to be the same):

```php
    <?php

    namespace A17\Twill\Models\Enums;

    use MyCLabs\Enum\Enum;

    class UserRole extends Enum
    {
        const CUSTOM1 = 'Custom role 1';
        const CUSTOM2 = 'Custom role 2';
        const CUSTOM3 = 'Custom role 3';
        const ADMIN = 'Admin';
    }
```

Finally, in your `AuthServiceProvider` class, redefine [Twill's default permissions](https://github.com/area17/twill/blob/e8866e40b7df4a6919e0ddb368990d04caeb705a/src/AuthServiceProvider.php#L26-L48) if you need to, or add your own, for example:

```php
    <?php

    namespace App\Providers;

    use A17\Twill\Models\Enums\UserRole;
    use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
    use Illuminate\Support\Facades\Gate;

    class AuthServiceProvider extends ServiceProvider
    {
        public function boot()
        {
            Gate::define('list', function ($user) {
                return in_array($user->role_value, [
                    UserRole::CUSTOM1,
                    UserRole::CUSTOM2,
                    UserRole::ADMIN,
                ]);
            });

        Gate::define('edit', function ($user) {
                return in_array($user->role_value, [
                    UserRole::CUSTOM3,
                    UserRole::ADMIN,
                ]);
            });

            Gate::define('custom-permission', function ($user) {
                return in_array($user->role_value, [
                    UserRole::CUSTOM2,
                    UserRole::ADMIN,
                ]);
            });
        }
    }
```

You can use your new permission and existing ones in many places like the `twill-navigation` configuration using `can`:

```php
    'projects' => [
        'can' => 'custom-permission',
        'title' => 'Projects',
        'module' => true,
    ],
```

Also in forms blade files using `@can`, as well as in middleware definitions in routes or controllers, see [Laravel's documentation](https://laravel.com/docs/5.7/authorization#via-middleware) for more info.

You should follow the Laravel documentation regarding [authorization](https://laravel.com/docs/5.3/authorization). It's pretty good. Also if you would like to bring administration of roles and permissions to the admin application, [spatie/laravel-permission](https://github.com/spatie/laravel-permission) would probably be your best friend.