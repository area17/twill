# Dashboard

Once you have created and configured multiple CRUD modules in your Twill's admin console, you can configure Twill's  dashboard in `config/twill.php`.

## Model activity

For each module that you want to enable in a part or all parts of the dashboard, add an entry to the `dashboard.modules` array, like in the following example:

```php
return [
    'dashboard' => [
        'modules' => [
            'projects' => [ // module name if you added a morph map entry for it, otherwise FQN of the model (eg. App\Models\Project)
                'name' => 'projects', // module name
                'label' => 'projects', // optional, if the name of your module above does not work as a label
                'label_singular' => 'project', // optional, if the automated singular version of your name/label above does not work as a label
                'routePrefix' => 'work', // optional, if the module is living under a specific routes group
                'count' => true, // show total count with link to index of this module
                'create' => true, // show link in create new dropdown
                'activity' => true, // show activities on this module in activities list
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

## Google Analytics

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

Follow [Spatie's documentation](https://github.com/spatie/laravel-analytics#how-to-obtain-the-credentials-to-communicate-with-google-analytics) to set up a Google service account and download a json file containing your credentials, and provide your Analytics view ID using the `ANALYTICS_VIEW_ID` environment variable.

## User activity

In addition to model activity, you can also enable user login/logout activity.

This feature is enabled by default and can be enabled by setting the following config keys:

```
twill.dashboard.auth_activity_log.login => true
twill.dashboard.auth_activity_log.logout => true
```

:::filename:::
`config/twill.php`
:::#filename:::

```php
<?php

return [
    'dashboard' => [
        'auth_activity_log' => [
            'login' => true,
            'logout' => true
        ]
    ]
]
```
