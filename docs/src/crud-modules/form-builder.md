---
pageClass: twill-doc
---

# Form builder

As of Twill 3.x you can, as an alternative to blade form files, define your forms from the module controller.

There are benefits and drawbacks to using the form builder or the form blade files:

**Benefits**

- More centralized
- Method exploration will show possibilities

**Drawbacks**

- Less control over appearance

## Using the form builder

To create a form from code you can add the `getForm` method to your controller.

The `getForm` method should return a `Form` object as illustrated below:

```php
<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController;
use A17\Twill\Services\Forms\Form;

class PageController extends ModuleController
{
    protected $moduleName = 'pages';

    public function getForm(\Illuminate\Database\Eloquent\Model $model): Form
    {
        return Form::make();
    }
}
```

## Adding fields

The `Form` object behind the scenes is a laravel Collection, this means you can use
collection methods to add fields.

```php
$form = Form::make();
$form->add(Input::make()->name('description'));
```

## Field methods

All form fields extend the `BaseFormField` class and have the same property options as explained in
the [form field docs](../form-fields/index.md). 

However, their names in some cases might be slightly different.

Fields in most cases require a `name` and `label`, but you can simply use `name` only as the `label` will be
automatically set, but will not be translatable.

```php
Input::make()
    ->name('description'); // Label: Description
Input::make()
    ->name('some_text'); // Label: Some text
```

If you want translated field labels you should use:

```php
Input::make()
    ->name('description')
    ->label(__('Description'))
```
