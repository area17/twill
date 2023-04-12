# Adding custom user roles and permissions on Twill 2

Objectives:

- Define a custom user role
- Limit the access of this role to a single module

## Context

Out of the box, Twill has 4 possible roles:

- SuperAdmin (not editable from the CMS)
- Admin
- Publisher
- View Only

In this example, we'll create a new `Author` role, as well as 2 new modules: `Pages` and `Posts`. Then, we'll limit the
access of the `Author` role to the `Posts` module only.

## Create the modules

```
php artisan twill:make:module posts
php artisan twill:make:module pages
```

Run the migrations:

```
php artisan migrate
```

Add the modules to our admin routes:

:::filename:::
`routes/twill.php`
:::#filename:::

```php
use A17\Twill\Facades\TwillRoutes;

TwillRoutes::module('pages');

TwillRoutes::module('posts');
```

... and to our navigation:

:::filename:::
`config/twill-navigation.php`
:::#filename:::

```php
return [
    'pages' => [
        'title' => 'Pages',
        'module' => true,
    ],
    'posts' => [
        'title' => 'Posts',
        'module' => true,
    ],
];
```

## Override Twill built-in roles

To define a new role, we need to override a file from within the Twill package. This can be done in a few steps, via
composer configuration. Let's start by defining the new role in the context of Twill default roles:

:::filename:::
`app/Models/Enums/UserRole.php`
:::#filename:::

```php
<?php

namespace A17\Twill\Models\Enums;

use MyCLabs\Enum\Enum;

class UserRole extends Enum
{
    const VIEWONLY = 'View only';
    const AUTHOR = 'Author';
    const PUBLISHER = 'Publisher';
    const ADMIN = 'Admin';
}
```

Conceptually, Authors are just below Publishers in terms of access-level. Publishers are able to create and update all
types of modules, but Authors are restricted to Posts only.

Then, let's update our composer autoload configuration:

:::filename:::
`composer.json`
:::#filename:::

```
    "autoload": {
        ...

        "files": ["app/Models/Enums/UserRole.php"],
        "exclude-from-classmap": ["vendor/area17/twill/src/Models/Enums/UserRole.php"]
    }
```

This tells composer to effectively replace Twill's file by the one we added in our project.

To enable the override, run:

```
composer dump-autoload
```

## Create the user accounts

Next, we'll log into Twill as SuperAdmin and create 2 new users:

- Alice, with a role of Publisher
- Bob, with a role of Author

After activating each user account, you'll notice that Alice has access to everything and that Bob has access to...
pretty much nothing, exept his own user profile. Let's keep going!

## Define the permissions for the Posts module

Like a standard Laravel application, Twill defines its permissions through gates in an `AuthServiceProvider` class. In
the same way, let's define 2 new permissions in our project:

:::filename:::
`app/Providers/AuthServiceProvider.php`
:::#filename:::

```php
use A17\Twill\Models\Enums\UserRole;

class AuthServiceProvider extends ServiceProvider
{
    // ...

    public function boot()
    {
        $this->registerPolicies();

        // The `list-posts` permission is granted to users of all roles
        Gate::define('list-posts', function ($user) {
            if ($user->isSuperAdmin()) {
                return true;
            }

            return in_array($user->role_value, [
                UserRole::VIEWONLY,
                UserRole::AUTHOR,
                UserRole::PUBLISHER,
                UserRole::ADMIN,
            ]);
        });

        // The `edit-posts` permission is granted to users of all roles, except `View Only`
        Gate::define('edit-posts', function ($user) {
            if ($user->isSuperAdmin()) {
                return true;
            }

            return in_array($user->role_value, [
                UserRole::AUTHOR,
                UserRole::PUBLISHER,
                UserRole::ADMIN,
            ]);
        });
    }
}
```

Then, we'll apply the `list-posts` permission to the `Posts` navigation item:

:::filename:::
`config/twill-navigation.php`
:::#filename:::

```php

    'posts' => [
        'title' => 'Posts',
        'module' => true,
        'can' => 'list-posts',
    ],
```

With this, Bob can now see the `Posts` item in the Twill navigation. However, Bob is getting a `Forbidden!` error
message when trying to access it. Almost there!

## Update the controller

We have applied the `list-posts` permission to the navigation, but what about the `edit-posts` permission? We'll need to
override 2 methods from the base `ModuleController` class to finish:

:::filename:::
`app/Http/Controllers/Admin/PostController.php`
:::#filename:::

```php
class PostController extends ModuleController
{
    // ...

    /**
     * On the base ModuleController, this is where built-in gates are assigned to each
     * controller action. Our `posts` module is only interested in our 2 custom gates,
     * for all possible actions.
     */
    protected function setMiddlewarePermission()
    {
        $this->middleware('can:list-posts', ['only' => ['index', 'show']]);
        $this->middleware('can:edit-posts', ['except' => ['index', 'show']]);
    }

    /**
     * On the base ModuleController, this is also used to assign built-in gates to specific
     * options defined in the `$indexOptions` array. In this simplified example, all index
     * options require the `edit-posts` permission.
     */
    protected function getIndexOption($option)
    {
        if (! \Auth::guard('twill_users')->user()->can('edit-posts')) {
            return false;
        }

        return ($this->indexOptions[$option] ?? $this->defaultIndexOptions[$option] ?? false);
    }
}
```

This effectively removes all Twill built-in gates for our module.

And there we have it, a new role is now available in our system!

## Using route groups

If your modules don't need to differentiate between `list` and `edit` permissions, you can move the middleware settings
to your admin routes instead of the controllers.

First, disable Twill's built-in gates on the module's controller:

:::filename:::
`app/Http/Controllers/Admin/PostController.php`
:::#filename:::

```php
class PostController extends ModuleController
{
    // ...

    protected function setMiddlewarePermission()
    {
    }

    protected function getIndexOption($option)
    {
        return ($this->indexOptions[$option] ?? $this->defaultIndexOptions[$option] ?? false);
    }
}
```

Then, add the route groups and middleware in the admin routes configuration:

:::filename:::
`routes/twill.php`
:::#filename:::

```php
use A17\Twill\Facades\TwillRoutes;

Route::group(['middleware' => 'can:edit-pages'], function () {
    TwillRoutes::module('pages');
});

Route::group(['middleware' => 'can:edit-posts'], function () {
    TwillRoutes::module('posts');
});

// ...
```

Route groups are especially useful if you want to define global permission groups for multiple modules (
e.g. `can:edit-blog`, `can:edit-site-content`, etc.)

## Cleanup AuthServiceProvider

Our `AuthServiceProvider` is functional but as we keep adding permissions, we can see that we'll end up with a quite a
bit of duplication.

To clean things up, we can use class constants to group common roles. We can also extend Twill's
own `AuthServiceProvider` class, which has two utility methods: `authorize()` and `userHasrole()`:

:::filename:::
`app/Providers/AuthServiceProvider.php`
:::#filename:::

```php
use A17\Twill\AuthServiceProvider as TwillAuthServiceProvider;

class AuthServiceProvider extends TwillAuthServiceProvider
{
    const ALL_ROLES = [UserRole::VIEWONLY, UserRole::AUTHOR, UserRole::PUBLISHER, UserRole::ADMIN];
    const ALL_EDITORS = [UserRole::AUTHOR, UserRole::PUBLISHER, UserRole::ADMIN];

    public function boot()
    {
        // `pages` module permissions
        Gate::define('list-pages', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, [UserRole::VIEWONLY, UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });
        Gate::define('edit-pages', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, [UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        // `posts` module permissions
        Gate::define('list-posts', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, self::ALL_ROLES);
            });
        });
        Gate::define('edit-posts', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, self::ALL_EDITORS);
            });
        });
    }
}
```

## Enabling the Media Library

At this point, if you have experimented a bit with posts and pages in your project, you may have noticed that authors
don't have access to the Media Library. Also in forms, authors can see the `medias` fields but can't add or change the
selected images in them.

Just like listing and editing modules, Twill has a few built-in gates to handle the Media Library permissions:

- the `list` permission is needed to browse the Media
  Library ([see _global_navigation.blade.php](https://github.com/area17/twill/blob/2.x/views/partials/navigation/_global_navigation.blade.php#L20))
- the `upload` permission is needed to display the `Add new`
  button ([see layouts/main.blade.php](https://github.com/area17/twill/blob/2.x/views/layouts/main.blade.php#L48-L50))
- the `edit` permission is also needed to process and save the uploaded
  images ([see MediaLibraryController.php](https://github.com/area17/twill/blob/2.x/src/Http/Controllers/Admin/MediaLibraryController.php#L83))

Here's our revised `AuthServiceProvider` to give authors full access to the Media Library:

:::filename:::
`app/Providers/AuthServiceProvider.php`
:::#filename:::

```php
class AuthServiceProvider extends TwillAuthServiceProvider
{
    const ALL_ROLES = [UserRole::VIEWONLY, UserRole::AUTHOR, UserRole::PUBLISHER, UserRole::ADMIN];
    const ALL_EDITORS = [UserRole::AUTHOR, UserRole::PUBLISHER, UserRole::ADMIN];

    public function boot()
    {
        // `list` permission is needed to access the Media Library
        Gate::define('list', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, self::ALL_ROLES);
            });
        });

        // `upload` and `edit` permissions are needed to upload to the Media Library
        Gate::define('upload', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, self::ALL_EDITORS);
            });
        });
        Gate::define('edit', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, self::ALL_EDITORS);
            });
        });

        // `pages` module permissions
        Gate::define('list-pages', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, [UserRole::VIEWONLY, UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });
        Gate::define('edit-pages', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, [UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        // `posts` module permissions
        Gate::define('list-posts', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, self::ALL_ROLES);
            });
        });
        Gate::define('edit-posts', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, self::ALL_EDITORS);
            });
        });
    }
}
```

**Important** Because `list` and `edit` are global permissions in Twill, when giving access to the Media Library, you
may need to add or adjust custom permissions on other modules to preserve the correct access levels.

## Where to go from here?

#### Explore Laravel documentation

We barely scratched the surface in terms of what is possible with gates within a Laravel project. You can learn more
in [Laravel's Autorization documentation](https://laravel.com/docs/10.x/authorization)

#### Explore Twill internals

With this, you have a good understanding of how permissions work within Twill. You can explore all the default gates
that are defined in Twill's [AuthServiceProvider](https://github.com/area17/twill/blob/2.x/src/AuthServiceProvider.php).
You can use this as a base to extend or change Twill's built-in permissions.

The base [ModuleController](https://github.com/area17/twill/blob/2.x/src/Http/Controllers/Admin/ModuleController.php)
class is also a great place to look for more fine-grained control over specific controller actions. (e.g. Allow certain
users to create and edit, but not to delete).

#### Try out the new permissions management feature

A complete overhaul of Twill's permissions system is being finalized for Twill 3.0. It adds user roles, groups and
item-level permissions, all configurable from within the CMS. You can find more information in the
following [pull request (#1138)](https://github.com/area17/twill/pull/1138), including notes on how to test this new
feature on a new project.

Thanks for reading and have fun :)

