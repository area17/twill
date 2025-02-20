# Validating blocks

There are 2 ways to add field validation to blocks. You have to use one or the other. If the blade directive is present it will be used over the class.

## Blade directive

The first and easiest option is to add the directives to your block form blade file:

There are 2 possible directives to use, these can be combined.

`@twillBlockValidationRules` for validating non translatable fields.

`@twillBlockValidationRulesForTranslatedFields` for validating translatable fields.

Both take an array of `[field => rules]` values. All Laravel validation rules are supported.

Example:

```blade
@twillBlockValidationRules([
    'text' => 'required'
])
@twillBlockValidationRulesForTranslatedFields([
    'title' => 'required'
])
```

## Block class

If you need more control you can use the block class to set validation rules, or even hook into the validation process.

See [Block classes documentation](./07_block-classes.md) for more details about the block class.

```php
<?php

namespace App\Twill\Block;

use A17\Twill\Services\Blocks\Block;

class ExampleBlock extends Block
{
    public $rulesForTranslatedFields = [
        'title' => 'required|string',
    ];

    public $rules = [
        'text' => 'required',
    ];
}
```
