---
pageClass: twill-doc
---

# Creating a Block Editor

#### Include the block editor in your module's form

In order to add a block editor to your module, add the `block_editor` field to your module form. e.g.:

```php
@extends('twill::layouts.form')

@section('contentFields')
    @formField('input', [
        'name' => 'description',
        'label' => 'Description',
    ])
...
    @formField('block_editor')
@stop
```

By default, adding the `@formField('block_editor')` directive enables all available *blocks* for use in your module. To scope only certain *blocks* to be available in a given module, you can add a second parameter to the `@formField()` directive with the *blocks* key. e.g.:

```php
@formField('block_editor', [
    'blocks' => ['quote', 'image']
])
```

#### Create and define blocks

Blocks and Repeaters are built on the same Block model and are created and defined in their respective folders. By default, Twill will look for Blade templates in `views/admin/blocks` for blocks and `views/admin/repeaters` for repeaters.

Note: Prior to Twill version 2.2, Blocks (and Repeaters) needed to be defined in the configuration file – this is no longer necessary and not recommended. This change is backward compatible, so your existing configuration should work as it used to. Defining blocks in the configuration file will be deprecated in a future release (see the section below [Legacy configuration](/block-editor/legacy-configuration-2-2.html).

Blocks (and Repeaters) are exactly like a regular form, without any Blade layout or section. The templates take special annotations to add further customization. The title annotation is mandatory and Twill will throw an error if it is not defined.

Available annotations:
  - Provide a title with `@twillPropTitle` or `@twillBlockTitle` or `@twillRepeaterTitle` (mandatory)
  - Provide a dynamic title with `@twillPropTitleField` or `@twillBlockTitleField` or `@twillRepeaterTitleField`
  - Provide an icon with `@twillPropIcon` or `@twillBlockIcon` or `@twillRepeaterIcon`
  - Provide a group with `@twillPropGroup` or `@twillBlockGroup` or `@twillRepeaterGroup` (defaults to `app`)
  - Provide a repeater trigger label with `@twillPropTrigger` or `@twillRepeaterTrigger`
  - Provide a repeater max items with `@twillPropMax` or `@twillRepeaterMax`, `@twillRepeaterMax` can also be defined from
the formField. See [Repeater form field](/form-fields/repeater.html)
  - Define a block or repeater as compiled with `@twillPropCompiled` or `@twillBlockCompiled` or `@twillRepeaterCompiled`
  - Define a block or repeater component with `@twillPropComponent` or `@twillBlockComponent` or `@twillRepeaterComponent`

e.g.:

filename: ```views/admin/blocks/quote.blade.php```
```php
@twillBlockTitle('Quote')
@twillBlockIcon('text')

@formField('input', [
    'name' => 'quote',
    'type' => 'textarea',
    'label' => 'Quote text',
    'maxlength' => 250,
    'rows' => 4
])
```

A more complex example would look like this:

filename: ```views/admin/blocks/media.blade.php```
```php
@twillBlockTitle('Media')
@twillBlockIcon('image')

@formField('medias', [
    'name' => 'image',
    'label' => 'Images',
    'withVideoUrl' => false,
    'max' => 20,
])

@formField('files', [
    'name'  => 'video',
    'label' => 'Video',
    'note'  => 'Video will overwrite previously selected images',
    'max'   => 1
])

@formField('input', [
    'name' => 'caption',
    'label' => 'Caption',
    'maxlength' => 250,
    'translated' => true,
])

@formField('select', [
    'name' => 'effect',
    'label' => 'Transition Effect',
    'placeholder' => 'Select Transition Effect',
    'default' => 'cut',
    'options' => [
        [
            'value' => 'cut',
            'label' => 'Cut'
        ],
        [
            'value' => 'fade',
            'label' => 'Fade In/Out'
        ]
    ]
])

@formField('color', [
    'name'  => 'bg',
    'label' => 'Background color',
    'note'  => 'Default is light grey (#E6E6E6)',
])

@formField('input', [
    'name' => 'timing',
    'label' => 'Timing',
    'maxlength' => 250,
    'note' => 'Timing in ms (default is 4000ms)',
])
```

With that, the *block* is ready to be used on the form!

##### Dynamic block titles

In Twill >= 2.5, you can use the `@twillBlockTitleField` directive to include the value of a given field in the title area of the blocks. This directive also accepts a `hidePrefix` option to hide the generic block title:

```php
@twillBlockTitle('Section')
@twillBlockTitleField('title', ['hidePrefix' => true])
@twillBlockIcon('text')
@twillBlockGroup('app')

@formField('input', [
    'name' => 'title',
    'label' => 'Title',
    'required' => true,
])

...
```

##### Create a block from an existing block template

Using `php artisan twill:make:block {name} {baseBlock} {icon}`, you can generate a new block based on a provided block as a base.

This example would create `views/admin/blocks/exceptional-media.blade.php` from `views/admin/blocks/media.blade.php`:

```
$ php artisan twill:make:block ExceptionalMedia media image
```

##### List existing blocks and repeaters

Using `php artisan twill:list:blocks` will list all blocks and repeaters. There are a few options:
  - `-s|--shorter` for a shorter table,
  - `-b|--blocks` for blocks only,
  - `-r|--repeaters` for repeaters only,
  - `-a|--app` for app blocks/repeaters only,
  - `-c|--custom` for app blocks/repeaters overriding Twill blocks/repeaters only,
  - `-t|--twill` for Twill blocks/repeaters only

##### List existing icons

`php artisan twill:list:icons` will list all icons available.

##### Using custom icons

Custom icons need to be named differently from default icons to avoid issues when creating the SVG sprites.

If you want to use custom icons in a block, you have to define the source directory's path in `config/twill.php`. Add it under `block_editor.directories.source.icons` key:

filename: ```config/twill.php```
```php
<?php

return [
    ...
    'block_editor' => [
        'directories' => [
            'source' => [
                'icons' => [
                    base_path('vendor/area17/twill/frontend/icons'),
                    resource_path('assets/admin/icons'), // or any other path of your choice
                ],
            ],
        ],
    ],
    ...
];
```
See also [Default Configuration](https://twill.io/docs/block-editor/default-configuration.html).

If the `resource_path('assets/admin/icons')` directory contains a `my-custom-icon.svg` file, you can use this icon in your block by using its basename: `@twillBlockIcon('my-custom-icon')`.


#### Use Block traits in your Model and Repository

Now, to handle the block data you must integrate it with your module. *Use* the *Blocks* traits in the Model and Repository associated with your module.
If you generated that module from the CLI and did respond yes to the question asking you about using blocks, this should already be the case for you.

In your model, use `HasBlocks`:

filename: ```app/Models/Article.php```
```php
<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Model;

class Article extends Model
{
    use HasBlocks;

    ...
}
```

In your repository, use `HandleBlocks`:

filename: ```app/Repositories/ArticleRepository.php```
```php
<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Article;

class ArticleRepository extends ModuleRepository
{
    use HandleBlocks;

    ...
}
```
