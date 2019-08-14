## Block editor
### Adding blocks
The block editor form field lets you add content freely to your module. The blocks can be easily added and rearranged.
Once a block is created, it can be used/added to any module by adding the corresponding traits.

In order to add a block editor you need to add the `block_editor` field to your module form. e.g.:

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

By adding the `@formField('block_editor')` you've enabled all the available blocks. To scope the *blocks* that will be displayed you can add a second parameter with the *blocks* key. e.g.:

```php
@formField('block_editor', [
    'blocks' => ['quote', 'image']
])
```

The *blocks* that can be added need to be defined under the `views/admin/blocks` folder.
The blocks can be defined exactly like a regular form. e.g.:

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

The task will generate a file inside the folder `resources/assets/js/blocks/`. Do not ignore those files in Git.

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

With that the *block* is ready to be used on the form, it needs to be enabled in the CMS configuration.
For it a `block_editor` key is required and inside you can define the list of `blocks` available in your project.

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

Please note the naming convention. If the *block* added is _quote_ then the component should be prefixed with _a17-block-_.
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


After having the blocks added and the configuration set it is required to have the traits added inside your module (Laravel Model).
Add the corresponding traits to your model and repository, respectively `HasBlocks` and `HandleBlocks`.

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

#### Common Errors
- Make sure your project has the blocks table migration. If not, you can find the `create_blocks_table` migration in Twill's source in `migrations`.

- Not running the _twill:blocks_ task.

- Not adding the *block* to the configuration.

- Not using the same name of the block inside the configuration.

- Not running npm run twill-build

### Adding repeater blocks
Lets say that it is requested to have an Accordion on Articles, where each item should have a _Header_ and a _Description_.
This accordion can be moved around along with the rest of the blocks.
On the Article (module) form we have:

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


- Add it on the config/twill.php
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

- Add it on the config/twill.php on the repeaters section

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
If you are requested to enable the possibility to add a related model, then the browser fields are the match.
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

- After that, it is required to add the Route Prefixes. e.g.:
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
To give an example:

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

You can enable the content editor individual block previews by providing a `resources/views/site/layouts/block.blade.php` blade layout file. The layout should be yielding a `content` section: `@yield('content')` with any frontend CSS/JS included exactly like in your main frontend layout. A simple example could be:

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
