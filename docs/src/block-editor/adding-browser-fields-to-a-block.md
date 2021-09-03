---
pageClass: twill-doc
title: Adding browser fields to a block
---

# Adding browser fields to a block


To attach other records inside of a block, it is possible to use the `browser` field.

- In a block, use the `browser` field:

filename: ```views/admin/blocks/products.blade.php```
```php
    @twillBlockTitle('Products')

    @formField('browser', [
        'routePrefix' => 'shop',
        'moduleName' => 'products',
        'name' => 'products',
        'label' => 'Products',
        'max' => 10
    ])
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

- When rendering the blocks on the frontend you can get the browser items selected in the block, by using the `browserIds` helper to retrieve the selected items' ids, and then you may use Eloquent method like `find` to get the actual records. Example in a blade template:

filename: ```views/site/blocks/blockWithBrowser.blade.php```
```php
    @php
      $selected_items_ids = $block->browserIds('browserFieldName');
      $items = Item::find($selected_items_ids);
    @endphp
```

- When the browser field allows multiple modules/endpoints, you can also use the `getRelated` function on the block:

filename: ```views/site/blocks/blockWithBrowser.blade.php```
```php
    @php
      $selected_items = $block->getRelated('browserFieldName');
    @endphp
```
