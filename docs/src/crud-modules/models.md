---
pageClass: twill-doc
---

# Models

Set your fillables to prevent mass-assignement. This is very important, as we use `request()->all()` in the module controller.

For fields that should default as null in the database when not sent by the form, use the `nullable` array.

For fields that should default to false in the database when not sent by the form, use the `checkboxes` array.

Depending upon the Twill features you need on your model, include the related traits and configure their respective options:

#### HasPosition

Implement the `A17\Twill\Models\Behaviors\Sortable` interface and add a position field to your fillables.

#### HasTranslation

Add translated fields in the `translatedAttributes` array.

Twill's `HasTranslation` trait is a wrapper around the popular `astronomic/laravel-translatable` package. A default configuration will be automatically published to your `config` directory when you run the `twill:install` command.

To setup your list of available languages for translated fields, modify the `locales` array in `config/translatable.php`, using ISO 639-1 two-letter languages codes as in the following example:

```php
<?php

return [
    'locales' => [
        'en',
        'fr',
    ],
    ...
];
```

#### HasSlug

Specify the field(s) used to create the slug in the `slugAttributes` array.

#### HasMedias

Add the `mediasParams` configuration array:

```php
<?php

public $mediasParams = [
    'cover' => [ // role name
        'default' => [ // crop name
            [
                'name' => 'default', // ratio name, same as crop name if single
                'ratio' => 16 / 9, // ratio as a fraction or number
            ],
        ],
        'mobile' => [
            [
                'name' => 'landscape', // ratio name, multiple allowed
                'ratio' => 16 / 9,
            ],
            [
                'name' => 'portrait', // ratio name, multiple allowed
                'ratio' => 3 / 4,
            ],
        ],
    ],
    '...' => [ // another role
        ... // with crops
    ]
];
```

#### HasFiles: 

Add the `filesParams` configuration array:

```php
<?php

public $filesParams = ['file_role', ...]; // a list of file roles
```

#### HasRevisions

No options.
