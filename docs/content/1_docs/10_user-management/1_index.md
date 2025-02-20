# User Management

Authentication and authorization are provided by default in Laravel. This package simply leverages what Laravel provides
and configures the views for you. By default, users can log in at `/login` and can also reset their password through that
same screen. New users have to reset their password before they can gain access to the admin application. By using the
twill configuration file, you can change the default redirect path (`auth_login_redirect_path`) and send users to
anywhere in your application following login.

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

There is also a super admin user that can impersonate other users at `/users/impersonate/{id}`.
The super admin can be a useful tool for testing features with different user roles without having to log out/login
manually,
as well as for debugging issues reported by specific users. You can stop impersonating by going
to `/users/impersonate/stop`.

## Extending user roles and permissions

You can create or modify new permissions for existing roles by using the Gate facade in your `AuthServiceProvider`.
The `can` middleware, provided by default in Laravel, is very easy to use, either through route definition or controller
constructor.

In `app/Models/Enums/UserRole.php` (or another file) define your roles:

```php
<?php

namespace App\Models\Enums;

use MyCLabs\Enum\Enum;

class UserRole extends Enum
{
    const CUSTOM1 = 'Custom role 1';
    const CUSTOM2 = 'Custom role 2';
    const CUSTOM3 = 'Custom role 3';
    const ADMIN = 'Admin';
}
```

Then in your app service provider you can register it:

```php
<?php
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \A17\Twill\Facades\TwillPermissions::setRoleEnum(\App\Models\Enums\UserRole::class);
    }
}
```

Finally, in your `AuthServiceProvider` class, redefine [Twill's default permissions](https://github.com/area17/twill/blob/e8866e40b7df4a6919e0ddb368990d04caeb705a/src/AuthServiceProvider.php#L26-L48) if you need to, or 
add your own, for example:

```php
<?php

namespace App\Providers;

use App\Models\Enums\UserRole;
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

If you need a more dynamic approach you can also get the current permission enum using the facade:

```php
TwillPermissons::roles()::PUBLISHER (or any role)
```

You can use your new permission and existing ones in many places like the `twill-navigation` configuration using `can`:

```php
    'projects' => [
        'can' => 'custom-permission',
        'title' => 'Projects',
        'module' => true,
    ],
```

Also in forms blade files using `@can`, as well as in middleware definitions in routes or controllers,
see [Laravel documentation](https://laravel.com/docs/10.x/authorization#via-middleware) for more info.

You should follow the Laravel documentation regarding [authorization](https://laravel.com/docs/10.x/authorization). It's
pretty good.

## Auto login

**DANGER**: don't use this feature in `production` as your CMS will be open for public.

Developers can configure Twill to do auto login using a pre-defined username and password and skip the login form.

To enable it you have to:

- Put the application in `debug` mode
- Create a user in the CMS
- Add user's credentials to your `.env` file:

``` dotenv
TWILL_AUTO_LOGIN_EMAIL=email@email.com
TWILL_AUTO_LOGIN_PASSWORD=passv0rt
```

- Enable the autologin feature:

``` dotenv
TWILL_AUTO_LOGIN_ENABLED=false
```

**Note**: this feature is available by default only for the `local` environment.
