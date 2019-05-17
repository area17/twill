## Block editor
### Overview
The block editor is a dynamic interface giving users a lot of flexibility in adding and changing content for a given entry.

For instance, if you have a module for creating work case studies (as we do in our demo), you can use the block editor to create and arrange blocks of images and text using an intuitive drag and drop interface.

![Block editor](/docs/_media/block-editor.png "Logo Title Text 1")

Once set up, the Block Editor makes it easy to add, arrange and update Blocks can be easily added and rearranged.

You can create any number of different block types, each with a unique form that can be accessed directly within the Block Editor.

[//]: # (Screengrab of a block edit form in the block editor)

Below, we describe the process of creating a block editor and connecting it to your module.

Here is an overview of the process, each of which is detailed below.

1. Include the Block Editor in your module
2. Define Blocks
3. Generate the block Vue component
4. Update twill configuration
5. Use Block traits in your Model and Repository

Before you begin, make sure that you have the blocks table migrated into your database. If you don't, add the `create_blocks_table` migration, which can be found in Twill's source in `migrations`.

Now, let's expand upon those steps:

#### Include the Block Editor in your module
In order to add a block editor to your module, add the `block_editor` field to your module form. e.g.:

[//]: # (Screengrab of block editor button)

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

By default, adding the `@formField('block_editor')` enables all available blocks for use in your module. To scope the *blocks* available in a given module, you can add a second parameter to the `@formField()` tag with the *blocks* key. e.g.:

```php
@formField('block_editor', [
    'blocks' => ['quote', 'image']
])
```

#### Define Blocks
The *blocks* need to be defined under the `views/admin/blocks` folder.
The blocks can be defined exactly like a regular form. For example, to create a *Quote* block, create the following:

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
The form can have multiple fields, giving you a great deal of power in content creation.

### Generate the block Vue component

Once the form is created an _artisan_ task needs to be run to generate the _Vue_ component for this block.

`php artisan twill:blocks`

Example output:
```shell
$ php artisan twill:blocks
Starting to scan block views directory...
Block Quote generated successfully
All blocks have been generated!
$
```

The task will generate a file inside the folder `resources/assets/js/blocks/`. **Do not ignore these files in Git.**

##### Example
filename: ```resources/assets/js/blocks/BlockQuote.vue```

```js
<template>
    <div class="block__body">
        <a17-textfield label="Quote text" :name="fieldName('quote')" type="textarea" :maxlength="250" :rows="4" in-store="value" ></a17-textfield>
    </div>
</template>

<script>
  import BlockMixin from '@/mixins/block'

  export default {
    mixins: [BlockMixin]
  }
</script>

```

With that, the *block* is ready to be used on the form! Now, you will need to enable it in the CMS configuration.

#### Update twill configuration

In the ```config/twill.php``` file, a `block_editor` array is required; inside the array, define all *blocks* available in your project, including the block title, the icon used when displaying it and the associated vue component you just generated. In this case, it would look like this.

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
            ..
        ]
    ]
```

**Please note the naming convention. If the *block* added is _quote_ then the component should be prefixed with _a17-block-_.**

If you added a block like *my_awesome_block* then you will need to keep the same name as _key_ and the _component name_ with the prefix. e.g.:
```php
    'block_editor' => [
        'blocks' => [
            ...
            'my_awesome_block' => [
                'title' => 'Title for my awesome block',
                'icon' => 'text',
                'component' => 'a17-block-my_awesome_block',
            ],
            ..
        ]
```

Nice!

#### Use Block traits in your Model and Repository

Now, to handle the block data you must integrate it with your module. *use* the *Blocks* traits in the Model and Repository associated with your module.

In your model use `HasBlocks`:

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

In your repository use `HandleBlocks`:

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

now run `npm run twill-build` in your terminal, and you are in business!

#### Common Errors
- Make sure your project has the blocks table migration. If not, you can find the `create_blocks_table` migration in Twill's source in `migrations`.

- Not running the _twill:blocks_ task.

- Not adding the *block* to the configuration.

- Not using the same name of the block inside the configuration.

- Not running npm run twill-build

### Adding repeater blocks
Let's say that you have an Articles module, and you would like to include an _Accordion_ section, where you would like any number of accordion items, each of which having a _Header_ and a _Description_. You would like the entire accordion to be treated as a block, to be dragged and dropped wherever you need it.

- On the Article (module) form make sure there is a _block_editor_ form field:

filename: ```views/admin/articles/form.blade.php```
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

- Inside the *container block* file, add a repeater form field:

  filename: ```admin/blocks/accordion.blade.php```
```php
  @formField('repeater', ['type' => 'accordion_item'])
```

Add it on the config/twill.php

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

- Add the *item block*, the one that will be reapeated inside the *container block*
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
- Generate the Vue component by running `php artisan twill:blocks`

- Update config/twill.php
  - Add the block to the block_editor section
  - Add the repeater field to the repeaters section


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
- If you add the *container block* to the _repeaters_ section inside the config, it won't work, e.g.:
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

- Not adding the *item block* to the _repeaters_ section.


### Adding browser fields
Perhaps you want to manage some data as its own module, and then grab entries from that module in your Articles. For that case, twill includes a handy `Browser Field`.
If you have an Article that can have related products.

On the Article (entity) form we have:

filename: ```views/admin/articles/form.blade.php```
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

- Add the block editors that will handle the `Browser Field`
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

- Define the block in the configuration like any other block in the config/twill.php.
```php
    'blocks' => [
        ...
        'products' => [
            'title' => 'Products',
            'icon' => 'text',
            'component' => 'a17-block-products',
        ],
```

- After that, add the Route Prefixes. e.g.:
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
            'products' => 'content',
        ],
    ]
```

- To render a browser with items selected in the block, you can use the `browserIds` helper to retrieve the selected items' ids, and then you may use Eloquent method like `find` to get the actual records:
```php
    $selected_items_ids = $block->browserIds('browserFieldName');
    $items = Item::find($selected_items_ids);
```

### Rendering blocks
As long as you have access to a model instance that uses the HasBlocks trait in a view, you can call the `renderBlocks` helper on it to render the list of blocks that were created from the CMS. By default, this function will loop over all the blocks and their child blocks and render a Blade view located in `resources/views/site/blocks` with the same name as the block key you specified in your Twill configuration and module form. 

In the frontend templates, you can call the `renderBlocks` helper like this:

```php
{!! $item->renderBlocks() !!}
```

If you want to render child blocks (when using repeaters) inside the parent block, you can do the following:

```php
{!! $work->renderBlocks(false) !!}
```

If you need to swap out a block view for a specific module (let’s say you used the same block in 2 modules of the CMS but need different rendering), you can do the following:

```php
{!! $work->renderBlocks(true, [
  'block-type' => 'view.path',
  'block-type-2' => 'another.view.path'
]) !!}
```

In these Blade views, you will have access to a `$block`variable with a couple of helper functions available to retrieve the block content:

```php
{{ $block->input('inputNameYouSpecifiedInTheBlockFormField') }}
{{ $block->translatedinput('inputNameYouSpecifiedInATranslatedBlockFormField') }}
```

If the block has a media field, you can refer to the Media Library documentation below to learn about the `HasMedias` trait helpers.
To give an exemple:

```php
{{ $block->image('mediaFieldName', 'cropNameFromBlocksConfig') }}
{{ $block->images('mediaFieldName', 'cropNameFromBlocksConfig')}}
```

### Default configuration

```php
return [
    'block_editor' => [
        'block_single_layout' => 'site.layouts.block',
        'block_views_path' => 'site.blocks',
        'block_views_mappings' => [],
        'block_preview_render_childs' => true,
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
    ],
    ...
];
```

### Content Editor

You can enable the content editor individual block previews by providing a `resources/views/site/layouts/block.blade.php` lade layout file. The layout should be yielding a `content` section: `@yield('content')` with any frontend CSS/JS included exactly like in your main frontend layout. A simple example could be:

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

If you would like to specify a custom layout view path, you can do so in `config/twill.php` at `twill.block_editor.block_single_layout`. In order to share the most of the layout between your frontend and individual blocks (essentially its assets), you can also create a parent layout and extend it from both.
