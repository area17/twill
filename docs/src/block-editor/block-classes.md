---
pageClass: twill-doc
---

# Block classes

If you need more control over blocks, their validation or data for rendering you can use a block class.

To do this create a file named after your block. (ex. for `images_grid.blade.php` your class will be
`ImagesGridBlock`)

A block class extends `A17\Twill\Services\Blocks\Block` and they are expected to be in the `App\Twill\Block` namespace:

```php
namespace App\Twill\Block;

use A17\Twill\Services\Blocks\Block;

class ExampleBlock extends Block
{
}
```

With a block class you can:

- [Customize block validation](/block-editor/validating-blocks.html#block-class)
- [Send more data properties to the rendering blade file](/block-editor/rendering-blocks.html#modifying-block-data)
- [Send more data properties to the form](/block-editor/adding-data-to-block-forms.html)
