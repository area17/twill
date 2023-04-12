# Browser

![screenshot](/assets/browser.png)

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::

:::tab=name.FormBuilder:::

```php
Browser::make()
    ->modules([Publications::class])
    ->name('publications')
    ->max(4);
```

:::#tab:::

:::tab=name.FormView:::

```blade
<x-twill::browser
    module-name="publications"
    name="publications"
    label="Publications"
    :max="4"
/>
```

:::#tab:::

:::tab=name.Directive:::

```blade
@formField('browser', [
    'moduleName' => 'publications',
    'name' => 'publications',
    'label' => 'Publications',
    'max' => 4,
])
```

:::#tab:::

:::#tabs:::

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

Inside the block editor, no migration is needed when using browsers. Refer to the section
titled [Adding browser fields to a block](../5_block-editor/04_adding-browser-fields-to-a-block.md) for a detailed
explanation.

Outside the block editor, browser fields are used to save `belongsToMany` relationships. The relationships can be stored
in Twill's own `related` table or in a custom pivot table.

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

- Add the browser field to `resources/views/twill/articles/form.blade.php`:

```php
@extends('twill::layouts.form')

@section('contentFields')
    ...

    <x-twill::browser
        module-name="authors"
        name="authors"
        label="Authors"
        :max="4"
    />
@stop
```

## Multiple browser fields referring to the same module

In some cases you may want to have 2 browser fields pointing to the same module. As by default the array elements
in `$relatedBrowsers` expect the module name and field name to match we can work around this. 

With the following form:

```php
$form->add(
    Browser::make()->modules([Page::class])->name('page_1'),
);

$form->add(
    Browser::make()->modules([Page::class])->name('page_2'),
);
```

We can setup `$relatedBrowsers` like this:

```php
protected $relatedBrowsers = [
    'page_1' => [
        'moduleName' => 'pages',
        'relation' => 'page_1'
    ],
    'page_2' => [
        'moduleName' => 'pages',
        'relation' => 'page_2'
    ]
];
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

- Add the browser field to `resources/views/twill/articles/form.blade.php`:

```php
@extends('twill::layouts.form')

@section('contentFields')
    ...

    <x-twill::browser
        :modules="[
            [
              'label' => 'Authors',
              'name' => 'authors',
            ],
            [
              'label' => 'Editors',
              'name' => 'editors',
            ],
        ]"
        name="collaborators"
        label="Collaborators"
        :max="4"
    />
@stop
```

- Alternatively, you can use manual endpoints instead of module names:

```php
<x-twill::browser
    :endpoints="[
        [
          'label' => 'Authors',
          'value' => '/authors/browser',
        ],
        [
          'label' => 'Editors',
          'value' => '/editors/browser',
        ],
    ]"
    name="collaborators"
    label="Collaborators"
    :max="4"
/>
```

## Working with related items

To retrieve the items in the frontend, you can use the `getRelated` method on models and blocks. It will return of
collection of related models in the correct order:

```php
    $item->getRelated('collaborators');

    // or, in a block:

    $block->getRelated('collaborators');
```

## Connecting 2 browser fields

The following example demonstrates how to make a browser field depend on the selected items of another browser field.

```php
<x-twill::browser
    module-name="products"
    name="product"
    label="Product"
    :max="1"
/>

<x-twill::browser
    label="Product variant"
    name="product_variant"
    module-name="productVariants"
    connected-browser-field="product"
    note="Select a product to enable this field"
    :max="1"
/>
```

The second browser is using the `connectedBrowserField` option, which will:

- add the connected browser's selected items IDs to the browser endpoint url, using the `connectedBrowserIds` query
  parameter,
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

In the presented example, this will make sure only variants of the selected product in the first browser can be selected
in the second one.

## Morphable browser fields

While a bit more complex to setup, you can target a morphTo.

For example we have a `MenuItem` model, and we want to target multiple types of models in our system.

In our `MenuItem` we add the relation to our `linkable`:

```php
    public function linkable()
    {
        return $this->morphTo();
    }
```

This goes with the following migration on the `menu_item`:

```php
$table->bigInteger('linkable_id')->nullable();
$table->string('linkable_type')->nullable();
```

Then in our `MenuItemRepository` we have to setup a few things:

```php
    // Prepare the fields.
    public function prepareFieldsBeforeCreate($fields)
    {
        $fields = parent::prepareFieldsBeforeCreate($fields);
        $fields['linkable_id'] = Arr::get($fields, 'browsers.linkables.0.id', null);
        $fields['linkable_type'] = Arr::get($fields, 'browsers.linkables.0.endpointType', null);

        return $fields;
    }

    // On save we set the linkable id and type.
    public function prepareFieldsBeforeSave($object, $fields)
    {
        $fields = parent::prepareFieldsBeforeSave($object, $fields);

        $id = Arr::get($fields, 'browsers.linkables.0.id', null);
        $type = Arr::get($fields, 'browsers.linkables.0.endpointType', null);

        if ($id) {
            $fields['linkable_id'] = $id;
        }
        if ($type) {
            $fields['linkable_type'] = $type;
        }

        return $fields;
    }

    // Set the browser value to our morphed data.
    public function getFormFields($object)
    {
        $fields = parent::getFormFields($object);

        $linkable = $object->linkable;

        if ($linkable) {
            $fields['browsers']['linkables'] = collect([
                [
                    'id' => $linkable->id,
                    'name' => $linkable->title,
                    'edit' => moduleRoute($object->linkable->getTable(), 'content', 'edit', $linkable->id),
                    'thumbnail' => $linkable->defaultCmsImage(['w' => 100, 'h' => 100]),
                ],
            ])->toArray();
        }

        return $fields;
    }
```

And finally in our form we can add the field:

```html

<x-twill::browser
    label="Link"
    :max="1"
    name="linkables"
    :modules="[
        [
            'label' => 'Homepages',
            'name' => 'homepages',
        ],
        [
            'label' => 'Pages',
            'name' => 'content.pages'
        ]
    ]"
/>
```
