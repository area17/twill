# Creating Blocks

In order to add a block editor to your module, add the block editor field to your module form:

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::

:::tab=name.FormBuilder:::

```php
BlockEditor::make()

BlockEditor::make()
    ->blocks(['title', 'quote', 'text'])
```

:::#tab:::

:::tab=name.FormView:::

```blade
<x-twill::block-editor />

@php
    $blocks = [
        'title',
        'quote',
        'text'
    ];
@endphp

<x-twill::block-editor
    :blocks="$blocks"
/>
```

:::#tab:::

:::tab=name.Directive:::

```blade
@formField('block_editor', [
    'blocks' => ['title', 'quote', 'text', 'image', 'grid', 'test', 'publications', 'news']
])
```

:::#tab:::

:::#tabs:::

## Block component class

As of Twill 3, you can make use of Block component classes.

These are essentially regular Blade components, but they are also responsible for your Block's form and rendering!

You can generate Block components using the command:

```
php artisan twill:make:componentBlock Namespace/Name
php artisan twill:make:componentBlock namespace.name
php artisan twill:make:componentBlock name
```

These blocks will be placed under `App\View\Components\Twill\Blocks`.

While the rendering blade file looks the same, there is no longer a form blade file.

Instead, you define the form in your component class, the same way you can do [module forms](../3_modules/7_form-builder.md)!

```php
<?php

namespace App\View\Components\Twill\Blocks;

use A17\Twill\Services\Forms\Fields\Wysiwyg;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Services\Forms\Fields\Input;
use A17\Twill\View\Components\Blocks\TwillBlockComponent;
use Illuminate\Contracts\View\View;

class Example extends TwillBlockComponent
{
    public function render(): View
    {
        return view('components.twill.blocks.example');
    }

    public function getForm(): Form
    {
        return Form::make([
            Input::make()->name('title'),
            Wysiwyg::make()->name('text')
        ]);
    }
}
```

### Block helpers

By default, the class name will be used as your block name, and 'app' will be the default group.

These can be overwritten by overriding the following methods:

```php
public static function getBlockTitle(?Block $block = null): string
{
    return Str::replace('Block', '', Str::afterLast(static::class, '\\'));
}

public static function getBlockGroup(): string
{
    return 'app';
}

public static function getBlockIcon(): string
{
    return 'text';
}
```

### Crops

Usually we would define image crop's in the block_editor config, but with block components you can define them inline in your component like this:

```php
public static function getCrops(): array
{
    return [
        'content_image' => [
            'default' => [
                [
                    'name' => 'default',
                    'ratio' => 16 / 9,
                    'minValues' => [
                        'width' => 100,
                        'height' => 100,
                    ],
                ],
            ]
        ]
    ];
}
```

### Validation

As with default blocks, you can also [validate](./08_validating-blocks.md) fields:

```php
public function getValidationRules(): array
{
    return [];
}

public function getTranslatableValidationRules(): array
{
    return [];
}
```

### Rendering helpers

You have access to all the same variables as in a regular block, however with the components you have some additional helpers:

#### input and translated input

With components, you can directly access input values like this:

```blade
{{ $input('title') }}
{{ $translatedInput('title') }}
```

#### Image url

Getting an image url:

```blade
{{ $image('cover', 'default', ['h' => 100) }}
```

#### Repeaters

Looping over a repeater:

```blade
@foreach ($repeater('slider-item') as $repeaterItem)
  <li>
    <img src="{{ $repeaterItem->renderData->block->image('slider', 'desktop', ['h' => 850]) }}" alt="">
    {{ $repeaterItem->renderData->block->input('title') }}
  </li>
@endforeach
```

### Block component in packages or domain specific directories

If you want to register blocks from your package you can add:

```php
\A17\Twill\Facades\TwillBlocks::registerComponentBlocks('\\Your\\Namespace\\Components\\Twill\\Blocks', __DIR__ . '/../../path/to/namespace');
```

This will register the namespace in your package or domain and load them!

## Using blade files

Blocks and Repeaters are built on the same Block model and are created and defined in their respective folders. By default, Twill will look for Blade templates in `views/twill/blocks` for blocks and `views/twill/repeaters` for repeaters.

Blocks (and Repeaters) are exactly like a regular form, without any Blade layout or section. The templates take special annotations to add further customization. The title annotation is mandatory and Twill will throw an error if it is not defined.

Available annotations:

- Provide a title with `@twillPropTitle` or `@twillBlockTitle` or `@twillRepeaterTitle` (mandatory)
- Provide a dynamic title with `@twillPropTitleField` or `@twillBlockTitleField` or `@twillRepeaterTitleField`
- Provide an icon with `@twillPropIcon` or `@twillBlockIcon` or `@twillRepeaterIcon`
- Provide a group with `@twillPropGroup` or `@twillBlockGroup` or `@twillRepeaterGroup` (defaults to `app`)
- Provide a repeater trigger label with `@twillPropTrigger` or `@twillRepeaterTrigger`
- Provide a repeater max items with `@twillPropMax` or `@twillRepeaterMax`, `@twillRepeaterMax` can also be defined from the formField. See [Repeater form field](../4_form-fields/repeater.md)
- Define a block or repeater as compiled with `@twillPropCompiled` or `@twillBlockCompiled` or `@twillRepeaterCompiled`
- Define a block or repeater component with `@twillPropComponent` or `@twillBlockComponent` or `@twillRepeaterComponent`

e.g.:

:::filename:::
`views/twill/blocks/quote.blade.php`
:::#filename:::

```blade
@twillBlockTitle('Quote')
@twillBlockIcon('text')

<x-twill::input 
    name="quote"
    type="textarea"
    label="Quote text"
    :maxlength="250"
    :rows="4"
/>
```

A more complex example would look like this:

:::filename:::
`views/twill/blocks/media.blade.php`
:::#filename:::

```php
@twillBlockTitle('Media')
@twillBlockIcon('image')

<x-twill::medias
    name="image"
    label="Images"
    :max="20"
/>

<x-twill:files
    name="video"
    label="video"
    note="Video will overwrite previously selected images"
    :max="1"
/>

<x-twill::input
    name="caption"
    label="Caption"
    :maxlength="250"
    :translated="true"
/>

@php
    $options = [
        [
            'value' => 'cut',
            'label' => 'Cut'
        ],
        [
            'value' => 'fade',
            'label' => 'Fade In/Out'
        ]
    ];
@endphp

<x-twill::select
    name="effect"
    label="Transition effect"
    placeholder="Select transition effect"
    default="cut"
    :options="$options"
/>

<x-twill::color
    name="bg"
    label="Background color"
    note="Default is light grey (#E6E6E6)"
/>

<x-twill::input
    name="timing"
    label="Timing"
    note="Timing in ms (default is 4000ms)"
/>
```

#### Dynamic block titles

In Twill >= 2.5, you can use the `@twillBlockTitleField` directive to include the value of a given field in the title area of the blocks. This directive also accepts a `hidePrefix` option to hide the generic block title:

```blade
@twillBlockTitle('Section')
@twillBlockTitleField('title', ['hidePrefix' => true])
@twillBlockIcon('text')
@twillBlockGroup('app')

<x-twill::input
    name="title"
    label="Title"
    :required="true"
/>
...
```

## Create a block from an existing block template

Using `php artisan twill:make:block {name} {baseBlock} {icon}`, you can generate a new block based on a provided block as a base.

This example would create `views/twill/blocks/exceptional-media.blade.php` from `views/twill/blocks/media.blade.php`:

```
$ php artisan twill:make:block ExceptionalMedia media image
```

## List existing blocks and repeaters

Using `php artisan twill:list:blocks` will list all blocks and repeaters. There are a few options:

- `-s|--shorter` for a shorter table,
- `-b|--blocks` for blocks only,
- `-r|--repeaters` for repeaters only,
- `-a|--app` for app blocks/repeaters only,
- `-c|--custom` for app blocks/repeaters overriding Twill blocks/repeaters only,
- `-t|--twill` for Twill blocks/repeaters only

## List existing icons

`php artisan twill:list:icons` will list all icons available.

## Using custom icons

Custom icons need to be named differently from default icons to avoid issues when creating the SVG sprites.

If you want to use custom icons in a block, you have to define the source directory's path in `config/twill.php`. Add it under `block_editor.directories.source.icons` key:

:::filename:::
`config/twill.php`
:::#filename:::

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

See also [Default Configuration](./11_default-configuration.md).

If the `resource_path('assets/admin/icons')` directory contains a `my-custom-icon.svg` file, you can use this icon in your block by using its basename: `@twillBlockIcon('my-custom-icon')`.

## Use Block traits in your Model and Repository

Now, to handle the block data you must integrate it with your module. *Use* the *Blocks* traits in the Model and Repository associated with your module.
If you generated that module from the CLI and did respond yes to the question asking you about using blocks, this should already be the case for you.

In your model, use `HasBlocks`:

:::filename:::
`app/Models/Article.php`
:::#filename:::

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

:::filename:::
`app/Repositories/ArticleRepository.php`
:::#filename:::

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
