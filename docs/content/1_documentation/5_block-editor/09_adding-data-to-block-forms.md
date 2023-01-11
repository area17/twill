# Adding data to block forms

When needed you can expose additional data to block forms using a [Block class](./07_block-classes.md).

:::alert=type.warning:::
The `getFormData` is only called once per module form and is not context aware.
:::#alert:::

```php
<?php

namespace App\Twill\Block;

use A17\Twill\Services\Blocks\Block;

class TextBlock extends Block
{
    public function getFormData(): array
    {
        return ['bar' => 'foo'];
    }
}
```

Now in your form you can use it as:

```blade
@twillBlockTitle('Text')
@twillBlockIcon('text')
@twillBlockGroup('app')

@if ($bar === 'foo')
... Conditional form fields
@endif
```
