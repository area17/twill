---
pageClass: twill-doc
---

# User Management

Authentication and authorization are provided by default in Laravel. This package simply leverages what Laravel provides and configures the views for you. By default, users can login at `/login` and can also reset their password through that same screen. New users have to reset their password before they can gain access to the admin application. By using the twill configuration file, you can change the default redirect path (`auth_login_redirect_path`) and send users to anywhere in your application following login.

## Roles

The package currently provides three different roles:
- view only
- publisher
- admin

## Permissions

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

## Extending user roles and permissions

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
