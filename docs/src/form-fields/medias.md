---
pageClass: twill-doc
---

# Medias

![screenshot](/docs/_media/medias.png)

```php
@formField('medias', [
    'name' => 'cover',
    'label' => 'Cover image',
    'note' => 'Also used in listings',
    'fieldNote' => 'Minimum image width: 1500px'
])

@formField('medias', [
    'name' => 'slideshow',
    'label' => 'Slideshow',
    'max' => 5,
    'fieldNote' => 'Minimum image width: 1500px'
])
```

| Option         | Description                                          | Type/values    | Default value |
| :------------- | :--------------------------------------------------- | :------------- | :------------ |
| name           | Name of the field                                    | string         |               |
| label          | Label of the field                                   | string         |               |
| translated     | Defines if the field is translatable                 | true<br/>false | false         |
| max            | Max number of attached items                         | integer        | 1             |
| fieldNote      | Hint message displayed above the field               | string         |               |
| note           | Hint message displayed in the field                  | string         |               |
| buttonOnTop    | Displays the `Attach images` button above the images | true<br/>false | false         |
| disabled            | Disables the field                                      | boolean         | false         | 


Right after declaring the `medias` formField in the blade template file, you still need to do a few things to make it work properly.

If the formField is in a static content form, you have to include the `HasMedias` Trait in your module's [Model](/crud-modules/models.html) and inlcude `HandleMedias` in your module's [Repository](/crud-modules/repositories.html). In addition, you have to uncomment the `$mediasParams` section in your Model file to let the model know about fields you'd like to save from the form.

Learn more about how Twill's media configurations work at [Model](/crud-modules/models.html), [Repository](/crud-modules/repositories.html), [Media Library Role & Crop Params](/media-library/image-rendering-service.html)

If the formField is used inside a block, you need to define the `mediasParams` at `config/twill.php` under `crops` key, and you are good to go. You could checkout [Twill Default Configuration](/block-editor/default-configuration.html) and [Rendering Blocks](/block-editor/rendering-blocks.html) for references.

If the formField is used inside a repeater, you need to define the `mediasParams` at `config/twill.php` under `block_editor.crops`.

If you need medias fields to be translatable (ie. publishers can select different images for each locale), set the `twill.media_library.translated_form_fields` configuration value to `true`.

##### Example

To add a `medias` form field in a form, first add `$mediaParams` to the model.

```php
<?php

namespace App\Models;

...
use A17\Twill\Models\Behaviors\HasMedias;
...
use A17\Twill\Models\Model;

class Post extends Model
{
    use ..., HasMedias;

    ...
    public $mediasParams = [
        'cover' => [
            'default' => [
                [
                    'name' => 'default',
                    'ratio' => 16 / 9,
                ],
            ],
            'mobile' => [
                [
                    'name' => 'mobile',
                    'ratio' => 1,
                ],
            ],
        ],
    ];

    ...
}
```

Then, add the form field to the `form.blade.php` file.

```php
@extends('twill::layouts.form')

@section('contentFields')

    ...

    @formField('medias', [
        'name' => 'cover',
        'label' => 'Cover image',
    ])

    ...
@stop
```

No migration is needed to save `medias` form fields.
