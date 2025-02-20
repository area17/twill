# Navigation

Since Twill 3.x there are 2 ways to manage navigation. You can only use one of them at the time. In general we suggest to use the newer approach by registering the navigation in your `AppServiceProvider`.

There are 3 levels of navigation.

- `Primary`: The most high level navigation.
- `Secondary`: Only visible if the primary parent navigation is active.
- `Tertiary`: Only visible if the secondary parent navigation is active.

## OOP method

To define navigation we use the boot method in our `AppServiceProvider`, below is an example based on the routing later in this file:

```php
use A17\Twill\Facades\TwillNavigation;
use A17\Twill\View\Components\Navigation\NavigationLink;

public function boot(): void
{
    TwillNavigation::addLink(
        NavigationLink::make()->forModule('pages')
    );
    TwillNavigation::addLink(
        NavigationLink::make()->forModule('projects')
          ->setChildren([
            NavigationLink::make()->forModule('projects')
              ->setChildren([
                NavigationLink::make()->forModule('projectCustomers'),
              ]),
            NavigationLink::make()->forModule('clients'),
            NavigationLink::make()->forModule('industries'),
            NavigationLink::make()->forModule('studios'),
          ]),
    );
}
```

```php
<?php

use A17\Twill\Facades\TwillRoutes;

TwillRoutes::module('pages');

Route::group(['prefix' => 'work'], function () {
    Route::group(['prefix' => 'projects'], function () {
        TwillRoutes::module('projectCustomers');
    });
    TwillRoutes::module('projects');
    TwillRoutes::module('clients');
    TwillRoutes::module('industries');
    TwillRoutes::module('studios');
});
```

## Legacy method

The `config/twill-navigation.php` file manages the navigation of your custom admin console. Using Twill's UI, 
the package provides 3 levels of navigation: global, primary and secondary. 
This file simply contains a nested array description of your navigation.

Each entry is defined by multiple options.
The simplest entry has a `title` and a `route` option which is a Laravel route name. 
A global entry can define a `primary_navigation` array that will contain more entries.
A primary entry can define a `secondary_navigation` array that will contain even more entries.
You can also add a `'target' => 'external'` option to open the link in a new window.

Two other options are provided that are really useful in conjunction with the CRUD modules you'll create in your application: `module` and `can`. `module` is a boolean to indicate if the entry is routing to a module route.
By default, it will link to the index route of the module you used as your entry key. `can` allows you to display/hide navigation links depending on the current user and permission name you specify.

#### Example

```php
<?php

return [
    'pages' => [
        'title' => 'Pages',
        'module' => true,
    ],
    'work' => [
        'title' => 'Work',
        'route' => 'admin.work.projects.index',
        'primary_navigation' => [
            'projects' => [
                'title' => 'Projects',
                'module' => true,
                'secondary_navigation' => [
                    'projectCustomers' => [
                        'title' => 'Customers',
                        'module' => true
                    ],
                ]
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

To make it work properly and to get active states automatically in Twill's UI, you should structure your routes in the same way as the example below:

```php
<?php

use A17\Twill\Facades\TwillRoutes;

TwillRoutes::module('pages');

Route::group(['prefix' => 'work'], function () {
    Route::group(['prefix' => 'projects'], function () {
        TwillRoutes::module('projectCustomers');
    });
    TwillRoutes::module('projects');
    TwillRoutes::module('clients');
    TwillRoutes::module('industries');
    TwillRoutes::module('studios');
});
```
