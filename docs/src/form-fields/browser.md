---
pageClass: twill-doc
---

# Browser

![screenshot](../.vuepress/public/_media/browser.png)

```php
@formField('browser', [
    'moduleName' => 'publications',
    'name' => 'publications',
    'label' => 'Publications',
    'max' => 4,
])
```

| Option                | Description                                                                     | Type    | Default value |
|:----------------------|:--------------------------------------------------------------------------------|:--------|:--------------|
| name                  | Name of the field                                                               | string  |               |
| label                 | Label of the field                                                              | string  |               |
| moduleName            | Name of the module (single related module)                                      | string  |               |
| modules               | Array of modules (multiple related modules), must include _name_                | array   |               |
| endpoints             | Array of endpoints (multiple related modules), must include _value_ and _label_ | array   |               |
| params                | Array of query params (key => value) to be passed to the browser endpoint       | array   |               |
| max                   | Max number of attached items                                                    | integer | 1             |
| note                  | Hint message displayed in the field                                             | string  |               |
| fieldNote             | Hint message displayed above the field                                          | string  |               |
| browserNote           | Hint message displayed inside the browser modal                                 | string  |               |
| itemLabel             | Label used for the `Add` button                                                 | string  |               |
| buttonOnTop           | Displays the `Add` button above the items                                       | boolean | false         |
| wide                  | Expands the browser modal to fill the viewport                                  | boolean | false         |
| sortable              | Allows manually sorting the attached items                                      | boolean | true          |
| disabled              | Disables the field                                                              | boolean | false         | 
| connectedBrowserField | Name of another browser field to connect to                                     | string  |               |

<br/>

Browser fields can be used inside as well as outside the block editor.

Inside the block editor, no migration is needed when using browsers. Refer to the section titled [Adding browser fields to a block](/block-editor/adding-browser-fields-to-a-block.html) for a detailed explanation.

Outside the block editor, browser fields are used to save `belongsToMany` relationships. The relationships can be stored in Twill's own `related` table or in a custom pivot table.

## Using browser fields as related items

The following example demonstrates how to use a browser field to attach `Authors` to `Articles`. 

- Update the `Article` model to add the `HasRelated` trait:

```php
use A17\Twill\Models\Behaviors\HasRelated;

class Article extends Model
{
    use HasRelated;

    /* ... */
}
```

- Update `ArticleRepository` to add the browser field to the `$relatedBrowsers` property:

```php
class ArticleRepository extends ModuleRepository
{
    protected $relatedBrowsers = ['authors'];
}
```

- Add the browser field to `resources/views/admin/articles/form.blade.php`:

```php
@extends('twill::layouts.form')

@section('contentFields')
    ...

    @formField('browser', [
        'moduleName' => 'authors',
        'name' => 'authors',
        'label' => 'Authors',
        'max' => 4,
    ])
@stop
```

## Multiple modules as related items

You can use the same approach to handle polymorphic relationships through Twill's `related` table.

- Update `ArticleRepository`:

```php
class ArticleRepository extends ModuleRepository
{
    protected $relatedBrowsers = ['collaborators'];
}
```

- Add the browser field to `resources/views/admin/articles/form.blade.php`:

```php
@extends('twill::layouts.form')

@section('contentFields')
    ...

    @formField('browser', [
        'modules' => [
            [
              'label' => 'Authors',
              'name' => 'authors',
            ],
            [
              'label' => 'Editors',
              'name' => 'editors',
            ],
        ],
        'name' => 'collaborators',
        'label' => 'Collaborators',
        'max' => 4,
    ])
@stop
```

- Alternatively, you can use manual endpoints instead of module names:

```php
    @formField('browser', [
        'endpoints' => [
            [
              'label' => 'Authors',
              'value' => '/authors/browser',
            ],
            [
              'label' => 'Editors',
              'value' => '/editors/browser',
            ],
        ],
        'name' => 'collaborators',
        'label' => 'Collaborators',
        'max' => 4,
    ])
```

## Working with related items

To retrieve the items in the frontend, you can use the `getRelated` method on models and blocks. It will return of collection of related models in the correct order:

```php
    $item->getRelated('collaborators');

    // or, in a block:

    $block->getRelated('collaborators');
```

## Using browser fields and custom pivot tables

Checkout this [Spectrum tutorial](https://spectrum.chat/twill/tips-and-tricks/step-by-step-ii-creating-a-twill-app~37c36601-1198-4c53-857a-a2b47c6d11aa) that walks through the entire process of using browser fields with custom pivot tables.

## Connecting 2 browser fields

The following example demonstrates how to make a browser field depend on the selected items of another browser field.

```php
@formField('browser', [
    'label' => 'Product',
    'name' => 'product',
    'moduleName' => 'products',
    'max' => 1,
])

@formField('browser', [
    'label' => 'Product variant',
    'name' => 'product_variant',
    'moduleName' => 'productVariants',
    'connectedBrowserField' => 'product',
    'note' => 'Select a product to enable this field.'
    'max' => 1,
])
```

The second browser is using the `connectedBrowserField` option, which will:

- add the connected browser's selected items IDs to the browser endpoint url, using the `connectedBrowserIds` query parameter,
- disable the browser field when the connected browser is empty,
- empty the browser field automatically when removing all items from the connected browser.

From your module's controller, you can then use `connectedBrowserIds` to do something like:

```php
public function getBrowserData($prependScope = [])
{
  if ($this->request->has('connectedBrowserIds')) {
    $products = collect(json_decode($this->request->get('connectedBrowserIds')));
    $prependScope['product_id'] = $products->toArray();
  }

  return parent::getBrowserData($prependScope);
}
```

In the presented example, this will make sure only variants of the selected product in the first browser can be selected in the second one.
