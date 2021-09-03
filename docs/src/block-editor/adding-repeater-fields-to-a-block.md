---
pageClass: twill-doc
title: Adding repeater fields to a block
---

# Adding repeater fields to a block


Inside a block, repeaters can be used too.

- Create a *container* block file, using a repeater form field:

  filename: ```views/admin/blocks/accordion.blade.php```
```php
  @twillBlockTitle('Accordion')
  ...
  @formField('repeater', ['type' => 'accordion_item'])
```
You can add other fields before or after your repeater, or even multiple repeaters to the same block.

- Create an *item* block, the one that will be reapeated inside the *container* block

filename: ```views/admin/repeaters/accordion_item.blade.php```
```php
  @twillRepeaterTitle('Accordion item')
  @twillRepeaterMax('10')

  @formField('input', [
      'name' => 'header',
      'label' => 'Header'
  ])

  @formField('input', [
      'type' => 'textarea',
      'name' => 'description',
      'label' => 'Description',
      'rows' => 4
  ])
```
