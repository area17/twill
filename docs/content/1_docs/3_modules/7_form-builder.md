# Form builder

As of Twill 3.x you can, as an alternative to blade form files, define your forms from the module controller.

There are benefits and drawbacks to using the form builder or the form blade files:

**Benefits**

- More centralized
- Method exploration will show possibilities

**Drawbacks**

- Less control over appearance (at this moment)

## Using the form builder

To create a form from code you can add the `getForm`, `getCreateForm` or `getSideFieldsets` method to your controller.

The `getForm`, `getCreateForm`, `getSideFieldsets` method should return a `Form` object as illustrated below:

```php
<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Forms\Form;


class PageController extends ModuleController
{
    protected $moduleName = 'pages';

    public function getForm(TwillModelContract $model): Form
    {
        return Form::make();
    }

    public function getCreateForm(): Form
    {
        return Form::make();
    }
    
    public function getSideFieldsets(TwillModelContract $model): Form
    {
        return new Form();
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
the [form field docs](../4_form-fields/index.md). 

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
    ->label(twillTrans('Description'))
```

## Fieldsets

It is also possible to add fieldsets to your form.

There are 2 `Form` methods that you can do this with:

**withFieldSets**

```php
$form->withFieldSets([
    Fieldset::make()->title('Fieldset 1')->id('fieldset')->fields([
      // Fields come here.
    ]),
    Fieldset::make()->title('Fieldset 2')->id('fieldset')->fields([
      // Fields come here.
    ])
]);
```

Or if you need more control:

**addFieldset**

```php
$form->addFieldset(
    Fieldset::make()->title('Fieldset!')->id('fieldset')->fields([
      // Fields come here.
    ])
);
```

## Other utilities

### Columns field

Using the `A17\Twill\Services\Forms\Columns` you can add a left/right split:

```php
Columns::make()
    ->left([
        Input::make()
            ->name('description')
            ->translatable(),
    ])
    ->right([
        Input::make()
            ->name('subtitle')
            ->translatable(),
    ]),
```

### Blade partial

You can also inject a blade file (which can hold a form in itself) as a field using `A17\Twill\Services\Forms\BladePartial`:

```php
BladePartial::make()->view('twill.fields.field-under-condition')
```
