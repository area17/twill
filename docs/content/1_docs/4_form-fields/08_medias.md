# Medias

![screenshot](/assets/medias.png)

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::
:::tab=name.FormBuilder:::

```php
Medias::make()
    ->name('cover')
    ->label(twillTrans('Cover image'))
    ->max(5)
```

:::#tab:::
:::tab=name.FormView:::

```blade
<x-twill::medias 
    name="cover" 
    label="Cover image"
    note="Also used in listings"
    field-note="Minimum image width: 1500px"
/>

<x-twill::medias 
    name="cover" 
    label="Cover image"
    note="Also used in listings"
    :max="5"
    field-note="Minimum image width: 1500px"
/>
```

:::#tab:::
:::tab=name.Directive:::

```blade
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

:::#tab:::
:::#tabs:::

| Option         | Description                                          | Type/values | Default value |
|:---------------|:-----------------------------------------------------|:------------|:--------------|
| name           | Name of the field                                    | string      |               |
| label          | Label of the field                                   | string      |               |
| translated     | Defines if the field is translatable                 | boolean     | false         |
| max            | Max number of attached items                         | integer     | 1             |
| fieldNote      | Hint message displayed above the field               | string      |               |
| note           | Hint message displayed in the field                  | string      |               |
| buttonOnTop    | Displays the `Attach images` button above the images | boolean     | false         |
| extraMetadatas | An array of additional metadatas, explained below    | array       | []            |
| disabled       | Disables the field                                   | boolean     | false         |

Right after declaring the `medias` formField in the blade template file, you still need to do a few things to make it
work properly.

If the formField is in a static content form, you have to include the `HasMedias` Trait in your
module's [Model](../3_modules/10_models.md) and inlcude `HandleMedias` in your
module's [Repository](../3_modules/11_repositories.md). In addition, you have to uncomment the `$mediasParams` section
in your Model file to let the model know about fields you'd like to save from the form.

Learn more about how Twill's media configurations work at [Model](../3_modules/10_models.md)
, [Repository](../3_modules/11_repositories.md)
, [Media Library Role & Crop Params](../7_media-library/03_role-crop-params.md)

If the formField is used inside a block, you need to define the `mediasParams` at `config/twill.php` under `crops` key,
and you are good to go. You could checkout [Twill Default Configuration](../5_block-editor/11_default-configuration.md)
and [Rendering Blocks](../5_block-editor/05_rendering-blocks.md) for references.

If the formField is used inside a repeater, you need to define the `mediasParams` at `config/twill.php`
under `block_editor.crops`.

If you need medias fields to be translatable (ie. publishers can select different images for each locale), set
the `twill.media_library.translated_form_fields` configuration value to `true`.

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
    <x-twill::medias
        name="cover"
        label="Cover image"
    />
    ...
@stop
```

No migration is needed to save `medias` form fields.

## Extra metadatas

On field level you can specify additional metadatas.

There are currently 2 supported field types:

- Text
- Checkbox

When defining your media field you can pass the extraMetadatas:

```php
@php
    $extraMetadata = [
        [
            'name' => 'credits_list',
            'label' => 'Credits list',
            'type' => 'text',
            'wysiwyg' => true,
            'wysiwygOptions' => [
                'modules' => [
                    'toolbar' => [
                        'italic',
                        'link'
                    ]
                ]
            ],
        ],
    ];
@endphp

<x-twill::medias 
    name="cover" 
    label="Cover image"
    note="Also used in listings"
    :extra-metadatas="$extraMetadata"
    field-note="Minimum image width: 1500px"
/>
```

The parameters `name`, `label` and `type` are mandatory, `wysiwyg` and `wysiwygOptions` are optional.

If no `wysiwygOptions` are provided it will fall back to the ones defined in
the [media config](../7_media-library/index.md)
