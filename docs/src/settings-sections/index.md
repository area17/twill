---
pageClass: twill-doc
---

# Settings Sections

Settings sections are standalone forms that you can add to your Twill's navigation to give publishers the ability to manage simple key/value records for you to then use anywhere in your application codebase.

Start by enabling the `settings` feature in your `config/twill.php` configuration file `enabled` array. See [Twill's configuration documentation](/enabled-features/) for more information.

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
