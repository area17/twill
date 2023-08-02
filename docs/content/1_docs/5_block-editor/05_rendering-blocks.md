# Rendering Blocks

When it is time to build a frontend, you will want to render a designed set of blocks, with all blocks in their proper order.
When working with a model instance that uses the HasBlocks trait in a view, you can call the `renderBlocks` helper on it.
This will render the list of blocks that were created from the CMS. By default, this function will loop over all the blocks and their child blocks.
In each case, the function will look for a Blade view to render for a given block.

Create views for your blocks in the `resources/views/site/blocks` directory. Their filenames should match the block key specified in your Twill configuration and module form.

For the `products` block example above, a corresponding view would be `resources/views/site/blocks/products.blade.php`.

You can call the `renderBlocks` helper within a *Blade* file. Such a call would look like this:

```blade
{!! $item->renderBlocks() !!}
```

If you have more block fields, you can get a specific one using:

```blade
{!! $item->renderNamedBlocks('field-name') !!}
```

You can also specify alternate blade views for blocks. This can be helpful if you use the same block in 2 different modules of the CMS, but you want to have design flexibility in how each is rendered.

To do that, specify the block view file in your call to the renderBlocks helper like this

```blade
{!! $work->renderBlocks([
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
{{ $block->images('mediaFieldName', 'cropNameFromBlocksConfig') }}
```

## Modifying block data

Sometimes it can be useful to abstract some PHP you would usually put at the top of the blade file.
This will keep your blade files cleaner and allow for easier logic writing.

See [Block classes documentation](../5_block-editor/07_block-classes.md) for more details about the block class.

```php
<?php

namespace App\Twill\Block;

use A17\Twill\Services\Blocks\Block;

class ImagesBlock extends Block
{
    public function getData(array $data, \A17\Twill\Models\Block $block): array
    {
        $data = parent::getData($data, $block);

        foreach ($block->imagesAsArrays('blog_image', 'desktop') as $imageData) {
            $data['images'][] = [
                'src' => $imageData['src'],
                'alt' => $imageData['alt']
            ];
        }

        return $data;
    }
}
```
