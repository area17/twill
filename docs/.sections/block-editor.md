## Block editor

### Overview

The block editor is a dynamic, drag and drop interface giving users a lot of flexibility in adding and changing content for a given entry.
For instance, if you have a module for creating work case studies (as we do in [our demo](https://demo.twill.io/)), you can use the block editor to create, arrange, and edit blocks of images and text, or anything else you can think of really, as they would appear in a page.
You can create any number of different block types, each with a unique form that can be accessed directly within the block editor.

Below, we describe the process of creating a block editor and connecting it to your module.

Here is an overview of the process, each of which is detailed below.

1. Include the block editor form field in your module's form
2. Create and define blocks
3. Make sure you use blocks traits in your Model and Repository

### Creating a block editor

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

Note: Prior to Twill version 2.2, Blocks (and Repeaters) needed to be defined in the configuration file – this is no longer necessary and not recommended. This change is backward compatible, so your existing configuration should work as it used to. Defining blocks in the configuration file will be deprecated in a future release (see the section below [Legacy configuration](#legacy-configuration-2-2)).

Blocks (and Repeaters) are exactly like a regular form, without any Blade layout or section. The templates take special annotations to add further customization. The title annotation is mandatory and Twill will throw an error if it is not defined.

Available annotations:
  - Provide a title with `@twillPropTitle` or `@twillBlockTitle` or `@twillRepeaterTitle` (mandatory)
  - Provide a dynamic title with `@twillPropTitleField` or `@twillBlockTitleField` or `@twillRepeaterTitleField`
  - Provide an icon with `@twillPropIcon` or `@twillBlockIcon` or `@twillRepeaterIcon`
  - Provide a group with `@twillPropGroup` or `@twillBlockGroup` or `@twillRepeaterGroup` (defaults to `app`)
  - Provide a repeater trigger label with `@twillPropTrigger` or `@twillRepeaterTrigger`
  - Provide a repeater max items with `@twillPropMax` or `@twillRepeaterMax`
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

### Adding repeater fields to a block

Inside a block, repeaters can be used too.

- Create a *container* block file, using a repeater form field:

  filename: ```views/admin/blocks/accordion.blade.php```
```php
  @twillBlockTitle('Accordion')
  ...
  @formField('repeater', ['type' => 'accordion_item'])
```
You can add other fields before or after your repeater, or even multiple repeaters to the same block.

- Create an *item* block, the one that will be repeated inside the *container* block

filename: ```views/admin/repeaters/accordion_item.blade.php```
```php
  @twillRepeaterTitle('Accordion item')
  @twillRepeaterMax('10')

  @formField('input', [
      'name' => 'header',
      'label' => 'Header'
  ])

  @formField('input', [
      'type' => 'textarea',
      'name' => 'description',
      'label' => 'Description',
      'rows' => 4
  ])
```

### Adding browser fields to a block

To attach other records inside of a block, it is possible to use the `browser` field.

- In a block, use the `browser` field:

filename: ```views/admin/blocks/products.blade.php```
```php
    @twillBlockTitle('Products')

    @formField('browser', [
        'routePrefix' => 'shop',
        'moduleName' => 'products',
        'name' => 'products',
        'label' => 'Products',
        'max' => 10
    ])
```

- If the module you are browsing is not at the root of your admin, you should use the `browser_route_prefixes` array in the configuration in addition to `routePrefix` in the form field declaration:

```php
    'block_editor' => [
        ...
        'browser_route_prefixes' => [
            'products' => 'shop',
        ],
        ...
    ],
```

- When rendering the blocks on the frontend you can get the browser items selected in the block, by using the `browserIds` helper to retrieve the selected items' ids, and then you may use Eloquent method like `find` to get the actual records. Example in a blade template:

filename: ```views/site/blocks/blockWithBrowser.blade.php```
```php
    @php
      $selected_items_ids = $block->browserIds('browserFieldName');
      $items = Item::find($selected_items_ids);
    @endphp
```

- When the browser field allows multiple modules/endpoints, you can also use the `getRelated` function on the block:

filename: ```views/site/blocks/blockWithBrowser.blade.php```
```php
    @php
      $selected_items = $block->getRelated('browserFieldName');
    @endphp
```

### Rendering blocks

When it is time to build a frontend, you will want to render a designed set of blocks, with all blocks in their proper order. When working with a model instance that uses the HasBlocks trait in a view, you can call the `renderBlocks` helper on it. This will render the list of blocks that were created from the CMS. By default, this function will loop over all the blocks and their child blocks. In each case, the function will look for a Blade view to render for a given block.

Create views for your blocks in the `resources/views/site/blocks` directory. Their filenames should match the block key  specified in your Twill configuration and module form.

For the `products` block example above, a corresponding view would be `resources/views/site/blocks/products.blade.php`.

You can call the `renderBlocks` helper within a *Blade* file. Such a call would look like this:

```php
{!! $item->renderBlocks() !!}
```

If you want to render child blocks (when using repeaters) inside the parent block, you can do the following:

```php
{!! $work->renderBlocks(false) !!}
```

You can also specify alternate blade views for blocks. This can be helpful if you use the same block in 2 different modules of the CMS, but you want to have design flexibility in how each is rendered. To do that, specify the block view file in your call to the renderBlocks helper like this

```php
{!! $work->renderBlocks(true, [
  'block-type' => 'view.path',
  'block-type-2' => 'another.view.path'
]) !!}
```

Within these Blade views, you will have access to a `$block` variable with helper functions available to retrieve the block content:

```php
{{ $block->input('inputNameYouSpecifiedInTheBlockFormField') }}
{{ $block->translatedinput('inputNameYouSpecifiedInATranslatedBlockFormField') }}
```

If the block has a media field, you can refer to the Media Library documentation below to learn about the `HasMedias` trait helpers. Here's an example of how a media field could be rendered:

```php
{{ $block->image('mediaFieldName', 'cropNameFromBlocksConfig') }}
{{ $block->images('mediaFieldName', 'cropNameFromBlocksConfig')}}
```

#### Modifying block data

Sometimes it can be useful to abstract some PHP you would usually put at the top of the blade file. This will keep your 
blade files cleaner and allow for easier logic writing.

For this you can use Block classes!

To do this create a file named after your block. (ex. for `hero_header_with_menu.blade.php` your class will be `HeroHeaderWithMenuBlock`)

The file needs to be in `app/Twill/Block` and has to extend `TwillBlock`.

```php
<?php

namespace App\Twill\Block;

use A17\Twill\Helpers\TwillBlock;

class HeroHeaderWithMenuBlock extends TwillBlock
{
    public function getData(): array
    {
        $image = $this->block->imageAsArray('heading', 'desktop');
        $ctaTarget = $this->block->getRelated('call_to_action_target')->first()->getSlug();

        return array_merge(
            parent::getData(),
            'image' => $image,
            'linkSlug' => $ctaTarget,
        );
    }
}
```

### Previewing blocks

At the top right of a form where you enabled a block editor, you will find a blue button labeled "Editor". The idea is to provide a better user experience when working with blocks, where the frontend preview is being immediately rendered next to the form, in a full-screen experience.

You can enable the content editor individual block previews by providing a `resources/views/site/layouts/block.blade.php` blade layout file. This file will be treated as a _layout_, so it will need to yield a `content` section: `@yield('content')`. It will also need to include any frontend CSS/JS necessary to give the block the look and feel of the corresponding frontend layout. Here's a simple example:

```php
<!doctype html>
<html>
    <head>
        <title>#madewithtwill website</title>
        <link rel="stylesheet" href="/css/app.css">
    </head>
    <body>
        <div>
            @yield('content')
        </div>
        <script src="/js/app.js"></script>
    </body>
</html>
```

If you would like to specify a custom layout view path, you can do so in `config/twill.php` at `twill.block_editor.block_single_layout`. A good way to share assets and structure from the frontend with these individual block previews is to create a parent layout and extend it from your block layout.

### Development workflow

As of verison 2.2, it is not necessary to rebuild Twill's frontend when working with blocks anymore. Their templates are now dynamically rendered in Blade and loaded at runtime by Vue. (For <2.1.x users, it means you do not need to run `php artisan twill:blocks` and `npm run twill-build` after creating or updating a block. Just reload the page to see your changes after saving your Blade file!)

This is possible because Twill's blocks Vue components are simple single file components that only have a template and a mixin registration. Blocks components are now dynamically registered by Vue using `x-template` scripts that are inlined by Blade.

#### Custom blocks and repeaters

To define a block as being `compiled` (ie. using a custom Vue component), you can do this with the annotations `@twillPropCompiled('true')`, `@twillBlockCompiled('true')` or `@twillRepeaterCompiled('true')`. The imported Vue file will be prefered at runtime over the inline, template only, version. 

You can bootstrap your custom Vue blocks by generating them from their Blade counterpart using `php artisan twill:blocks`. It will ask you to confirm before overriding any existing custom Vue block. To start a custom Vue block from scratch, use the following template:

```vue
<template>
    <!-- eslint-disable -->
    <div class="block__body">
        <!-- CUSTOM CODE -->
    </div>
</template>

<script>
  import BlockMixin from '@/mixins/block'

  export default {
    mixins: [BlockMixin]
  }
</script>

```

Note: For legacy 2.1.x users, in the `twill.block_editor.blocks` configuration array, set 'compiled' to `true` on the individual blocks.

If you are using custom Vue blocks (as in, you edited the `template`, `script` or `style` section of a generated block Vue file), you need to rebuild Twill assets.

There are two artisan commands to help you and we recommend using them instead of our previous versions' npm scripts:

 - `php artisan twill:build`, which will build Twill's assets with your custom blocks, located in the `twill.block_editor.custom_vue_blocks_resource_path` new configurable path (with defaults to `assets/js/blocks`, like in previous versions).

 - `php artisan twill:dev`, which will start a local server that watches for changes in Twill's frontend directory. You need to set `'dev_mode' => true` in your `config/twill.php` file when using this command. This is especially helpful for Twill's contributors, but can also be useful if you use a lot of custom components in your application.

Both commands take a `--noInstall` option to avoid running `npm ci` before every build.

#### Naming convention of custom Vue components

The naming convention for custom blocks Vue component is deferred from the block's component name. For example, if your block's component name is `a17-block-quote`, the custom blocks should be `assets/js/blocks/BlockQuote.vue`. For component name with underscores, for example `a17-amazing_quote`, it would be `assets/js/blocks/BlockAmazing_quote.vue`.

#### Disabling inline blocks' templates

It is also possible to completely disable this feature by setting the `twill.block_editor.inline_blocks_templates` config flag to `false`.

If you do disable this feature, you could continue using previous versions' npm scripts, but we recommend you stop rebuilding Twill assets entirely unless you are using custom code in your generated Vue blocks. If you do keep using our npm scripts instead of our new Artisan commands, you will need to update `twill-build` from:

```
  "twill-build": "rm -f public/hot && npm run twill-copy-blocks && cd vendor/area17/twill && npm ci && npm run prod && cp -R public/* ${INIT_CWD}/public",
```

to:

```
  "twill-build": "npm run twill-copy-blocks && cd vendor/area17/twill && npm ci && npm run prod && cp -R dist/* ${INIT_CWD}/public",
```

#### A bit further: extending Twill with custom components and custom workflows

On top of custom Vue blocks, It is possible to rebuild Twill with custom Vue components. This can be used to override Twill's own Vue components or create new form fields, for example. The `twill.custom_components_resource_path` configuration can be used to provide a path under Laravel `resources` folder that will be used as a source of Vue components to include in your form js build when running `php artisan twill:build`.

You have to run `php artisan twill:build` for your custom Vue components to be included in the frontend build.

For a more in depth tutorial, check out this [Spectrum post](https://spectrum.chat/twill/tips-and-tricks/adding-a-custom-block-to-twill-admin-view-with-vuejs~028d79b1-b3cd-4fb7-a89c-ce64af7be4af).

### Default configuration

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Twill Block Editor configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to provide the package with your configuration
    | for the Block editor field and Editor features.
    |
     */
    'block_single_layout' => 'site.layouts.block', // layout to use when rendering a single block in the editor
    'block_views_path' => 'site.blocks', // path where a view file per block type is stored
    'block_views_mappings' => [], // custom mapping of block types and views
    'block_preview_render_childs' => true, // indicates if childs should be rendered when using repeater in blocks
    'block_presenter_path' => null, // allow to set a custom presenter to a block model
    // Indicates if blocks templates should be inlined in HTML.
    // When setting to false, make sure to build Twill with your all your custom blocks.
    'inline_blocks_templates' => true,
    'custom_vue_blocks_resource_path' => 'assets/js/blocks',
    'use_twill_blocks' => ['text', 'image'],
    'crops' => [
        'image' => [
            'desktop' => [
                [
                    'name' => 'desktop',
                    'ratio' => 16 / 9,
                    'minValues' => [
                        'width' => 100,
                        'height' => 100,
                    ],
                ],
            ],
            'tablet' => [
                [
                    'name' => 'tablet',
                    'ratio' => 4 / 3,
                    'minValues' => [
                        'width' => 100,
                        'height' => 100,
                    ],
                ],
            ],
            'mobile' => [
                [
                    'name' => 'mobile',
                    'ratio' => 1,
                    'minValues' => [
                        'width' => 100,
                        'height' => 100,
                    ],
                ],
            ],
        ],
    ],
    'directories' => [
        'source' => [
            'blocks' => [
                [
                    'path' => base_path('vendor/area17/twill/src/Commands/stubs/blocks'),
                    'source' => A17\Twill\Services\Blocks\Block::SOURCE_TWILL,
                ],
                [
                    'path' => resource_path('views/admin/blocks'),
                    'source' => A17\Twill\Services\Blocks\Block::SOURCE_APP,
                ],
            ],
            'repeaters' => [
                [
                    'path' => resource_path('views/admin/repeaters'),
                    'source' => A17\Twill\Services\Blocks\Block::SOURCE_APP,
                ],
                [
                    'path' => base_path('vendor/area17/twill/src/Commands/stubs/repeaters'),
                    'source' => A17\Twill\Services\Blocks\Block::SOURCE_TWILL,
                ],
            ],
            'icons' => [
                base_path('vendor/area17/twill/frontend/icons'),
                resource_path('views/admin/icons'),
            ],
        ],
        'destination' => [
            'make_dir' => true,
            'blocks' => resource_path('views/admin/blocks'),
            'repeaters' => resource_path('views/admin/repeaters'),
        ],
    ],
];
```

### Legacy configuration (< 2.2)

#### Twill prior to version 2.2

For Twill version 2.1.x and below, in the `config/twill.php` `block_editor` array, define all *blocks* and *repeaters* available in your project, including the block title, the icon used when displaying it in the block editor form and the associated component name. It would look like this:

filename: ```config/twill.php```
```php
    'block_editor' => [
        'blocks' => [
            ...
            'quote' => [
                'title' => 'Quote',
                'icon' => 'text',
                'component' => 'a17-block-quote',
            ],
            'media' => [
                'title' => 'Media',
                'icon' => 'image',
                'component' => 'a17-block-media',
            ],
            'accordion' => [
                'title' => 'Accordion',
                'icon' => 'text',
                'component' => 'a17-block-accordion',
            ],
            ...
        ]
        'repeaters' => [
            'accordion_item' => [
                'title' => 'Accordion item',
                'icon' => 'text',
                'component' => 'a17-block-accordion_item',
            ],
            ...
        ],
    ],
```

**Please note the naming convention. If the *block* added is `quote` then the component should be prefixed with `a17-block-`.**

If you added a block named *awesome_block*, your configuration would look like this:

```php
    'block_editor' => [
        'blocks' => [
            ...
            'awesome_block' => [
                'title' => 'Title for the awesome block',
                'icon' => 'text',
                'component' => 'a17-block-awesome_block',
            ],
            ..
        ]
```

##### Common errors
- If you add the *container* block to the _repeaters_ section inside the config, it won't work, e.g.:
```php
        'repeaters' => [
            ...
            'accordion' => [
                'title' => 'Accordion',
                'trigger' => 'Add accordion',
                'component' => 'a17-block-accordion',
                'max' => 10,
            ],
            ...
        ]
```

- If you use a different name for the block inside the _repeaters_ section, it also won't work, e. g.:
```php
        'repeaters' => [
            ...
            'accordion-item' => [
                'title' => 'Accordion',
                'trigger' => 'Add accordion',
                'component' => 'a17-block-accordion_item',
                'max' => 10,
            ],
            ...
        ]
```

- Not adding the *item* block to the _repeaters_ section will also result in failure.
