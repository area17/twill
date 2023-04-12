# Adding Browser Fields to a Block

To attach other records inside a block, it is possible to use the `browser` field.

- In a block, use the `browser` field:

:::filename:::
`views/twill/blocks/products.blade.php`
:::#filename:::

```blade
@twillBlockTitle('Products')

<x-twill::browser
    route-prefix="shop"
    module-name="products"
    name="products"
    label="Products"
    :max="10"
/>
```

- If the module you are browsing is not at the root of your admin, you should use the `browser_route_prefixes` array in the configuration in addition to `routePrefix` in the form field declaration:

```php
'block_editor' => [
    ...
    'browser_route_prefixes' => [
        'products' => 'shop',
    ],
    ...
],
```

- When rendering the blocks on the frontend you can get the browser items selected in the block, by using the `getRelated` helper to retrieve the selected items. Example in a blade template:
- 
:::filename:::
`views/site/blocks/blockWithBrowser.blade.php`
:::#filename:::

```blade
@php
  $selected_items = $block->getRelated('browserFieldName');
@endphp
```
