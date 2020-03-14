## Block editor
### Overview
The block editor is a dynamic, drag and drop interface giving users a lot of flexibility in adding and changing content for a given entry.
For instance, if you have a module for creating work case studies (as we do in [our demo](https://demo.twill.io/)), you can use the block editor to create, arrange, and edit blocks of images and text, or anything else you can think of really, as they would appear in a page.
You can create any number of different block types, each with a unique form that can be accessed directly within the block editor.

Below, we describe the process of creating a block editor and connecting it to your module.

Here is an overview of the process, each of which is detailed below.

1. Include the block editor form field in your module's form
2. Define blocks
4. Update twill configuration
5. Make sure you use blocks traits in your Model and Repository

Now, let's expand upon those steps:

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

By default, adding the `@formField('block_editor')` directive enables all available *blocks* for use in your module. To scope only certain *blocks* to be available in a given module, you can add a second parameter to the `@formField()` directivee with the *blocks* key. e.g.:

```php
@formField('block_editor', [
    'blocks' => ['quote', 'image']
])
```

#### Define blocks

The *blocks* need to be defined under the `views/admin/blocks` folder.
The blocks can be defined exactly like a regular form, but without any Blade layout or section. e.g.:

filename: ```admin/blocks/quote.blade.php```
```php
@formField('input', [
    'name' => 'quote',
    'type' => 'textarea',
    'label' => 'Quote text',
    'maxlength' => 250,
    'rows' => 4
])
```

A more complex block could look like the following:

filename: ```admin/blocks/media.blade.php```
```php
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

With that, the *block* is ready to be used on the form! Now, you will need to enable it in the configuration.

#### Update Twill's configuration

In the `config/twill.php` file, a `block_editor` array is required; inside the array, define all *blocks* available in your project, including the block title, the icon used when displaying it and the associated component name. In this case, it would look like this:

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
            ..
        ]
    ]
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

#### Use Block traits in your Model and Repository

Now, to handle the block data you must integrate it with your module. *Use* the *Blocks* traits in the Model and Repository associated with your module.
If you generated that module from the CLI and did not respond no to the question asking you about using blocks, this should already be the case for you.

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

### Adding repeater blocks

Inside of a block, repeaters can be used too.

- Create a *container* block file, using a repeater form field:

  filename: ```admin/blocks/accordion.blade.php```
```php
  @formField('repeater', ['type' => 'accordion_item'])
```
You can add other fields before or after your repeater, or even multiple repeaters to the same block.

- Add your block to the configuration:
```php
    'block_editor' => [
        'blocks' => [
            ...
            'accordion' => [
                'title' => 'Accordion',
                'icon' => 'text',
                'component' => 'a17-block-accordion',
            ],
            ..
        ]
    ]
```

- Create an *item* block, the one that will be reapeated inside the *container* block

filename: ```admin/blocks/accordion_item.blade.php```
```php
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

- Add it to the configuration, in the repeaters section

```php
    'block_editor' => [
        'blocks' => [
            ...
            'accordion' => [
                'title' => 'Accordion',
                'icon' => 'text',
                'component' => 'a17-block-accordion',
            ],
            ..
        ],
        'repeaters' => [
            ...
            'accordion_item' => [
                'title' => 'Accordion',
                'trigger' => 'Add accordion',
                'component' => 'a17-block-accordion_item',
                'max' => 10,
            ],
            ...
        ]
    ]
```

#### Common errors:
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

### Adding browser fields

To attach other records to inside of a block, it is possible to use the `browser` field.

- In a block, use the `browser` field:
filename: ```views/admin/blocks/products.blade.php```
```php
    @formField('browser', [
        'routePrefix' => 'content',
        'moduleName' => 'products',
        'name' => 'products',
        'label' => 'Products',
        'max' => 10
    ])
```

- Define the block in configuration like any other block:
```php
    'blocks' => [
        ...
        'products' => [
            'title' => 'Products',
            'icon' => 'text',
            'component' => 'a17-block-products',
        ],
```

- If the module you are browsing is not at the root of your admin, you can use the `browser_route_prefixes` array:
```php
    'block_editor' => [
        'blocks' => [
            ...
            'product' => [
                'title' => 'Product',
                'icon' => 'text',
                'component' => 'a17-block-products',
            ],
            ...
        ],
        'repeaters' => [
                ...
        ],
        'browser_route_prefixes' => [
            'products' => 'shop',
        ],
    ]
```

- To render a browser with items selected in the block, you can use the `browserIds` helper to retrieve the selected items' ids, and then you may use Eloquent method like `find` to get the actual records:
```php
    $selected_items_ids = $block->browserIds('browserFieldName');
    $items = Item::find($selected_items_ids);
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

Within these Blade views, you will have access to a `$block`variable with helper functions available to retrieve the block content:

```php
{{ $block->input('inputNameYouSpecifiedInTheBlockFormField') }}
{{ $block->translatedinput('inputNameYouSpecifiedInATranslatedBlockFormField') }}
```

If the block has a media field, you can refer to the Media Library documentation below to learn about the `HasMedias` trait helpers. Here's an example of how a media field could be rendered:

```php
{{ $block->image('mediaFieldName', 'cropNameFromBlocksConfig') }}
{{ $block->images('mediaFieldName', 'cropNameFromBlocksConfig')}}
```

### Previewing blocks

At the top right of a form where you enabled a block editor, you will find a blue button labelled "Editor". The idea is to provide a better user experience when working with blocks, where the frontend preview is being immediately rendered next to the form, in a full screen experience.

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

### Default configuration

```php
return [
    'block_editor' => [
        'block_single_layout' => 'site.layouts.block', // layout to use when rendering a single block in the editor
        'block_views_path' => 'site.blocks', // path where a view file per block type is stored
        'block_views_mappings' => [], // custom mapping of block types and views
        'block_preview_render_childs' => true, // indicates if childs should be rendered when using repeater in blocks
        'block_presenter_path' => null, // allow to set a custom presenter to a block model
        // Indicates if blocks templates should be inlined in HTML.
        // When setting to false, make sure to build Twill with your all your custom blocks using php artisan twill:build.
        'inline_blocks_templates' => true,
        'custom_vue_blocks_resource_path' => 'assets/js/blocks', // path to custom vue blocks in your resources directory
        'blocks' => [
            'text' => [
                'title' => 'Body text',
                'icon' => 'text',
                'component' => 'a17-block-wysiwyg',
            ],
            'image' => [
                'title' => 'Image',
                'icon' => 'image',
                'component' => 'a17-block-image',
            ],
        ],
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
        'repeaters' => [],
    ],
    ...
];
```
