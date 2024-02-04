# Block classes

If you need more control over blocks, their validation or data for rendering you can use a block class.

To do this, create a file named after your block. (ex. for `images_grid.blade.php` your class will be
`ImagesGridBlock`)

A block class extends `A17\Twill\Services\Blocks\Block` and they are expected to be in the `App\Twill\Block` namespace:

```php
<?php

namespace App\Twill\Block;

use A17\Twill\Services\Blocks\Block;

class ExampleBlock extends Block
{
}
```

With a block class you can:

- [Customize block validation](./08_validating-blocks.md#content-block-class)
- [Send more data properties to the rendering blade file](./05_rendering-blocks.md#content-modifying-block-data)
- [Send more data properties to the form](./09_adding-data-to-block-forms.md)
