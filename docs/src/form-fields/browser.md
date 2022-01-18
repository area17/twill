---
pageClass: twill-doc
---

# Browser

![screenshot](/docs/_media/browser.png)

```php
@formField('browser', [
    'moduleName' => 'publications',
    'name' => 'publications',
    'label' => 'Publications',
    'max' => 4,
])
```

| Option      | Description                                                                     | Type    | Default value |
| :---------- | :------------------------------------------------------------------------------ | :-------| :------------ |
| name        | Name of the field                                                               | string  |               |
| label       | Label of the field                                                              | string  |               |
| moduleName  | Name of the module (single related module)                                      | string  |               |
| modules     | Array of modules (multiple related modules), must include _name_                | array   |               |
| endpoints   | Array of endpoints (multiple related modules), must include _value_ and _label_ | array   |               |
| max         | Max number of attached items                                                    | integer | 1             |
| note        | Hint message displayed in the field                                             | string  |               |
| fieldNote   | Hint message displayed above the field                                          | string  |               |
| browserNote | Hint message displayed inside the browser modal                                 | string  |               |
| itemLabel   | Label used for the `Add` button                                                 | string  |               |
| buttonOnTop | Displays the `Add` button above the items                                       | boolean | false         |
| wide        | Expands the browser modal to fill the viewport                                  | boolean | false         |
| sortable    | Allows manually sorting the attached items                                      | boolean | true          |

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

- Add the browser field to `resources/views/twill/admin/articles/form.blade.php`:

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

- Add the browser field to `resources/views/twill/admin/articles/form.blade.php`:

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
