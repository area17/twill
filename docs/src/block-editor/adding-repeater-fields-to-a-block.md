---
pageClass: twill-doc
---

# Adding Repeater Fields to a Block

Inside a block, repeaters can be used too.

- Create a *container* block file, using a repeater form field:

  filename: ```views/twill/blocks/accordion.blade.php```
```php
  @twillBlockTitle('Accordion')
  ...
  @formField('repeater', ['type' => 'accordion_item'])
```
You can add other fields before or after your repeater, or even multiple repeaters to the same block.

- Create an *item* block, the one that will be repeated inside the *container* block

filename: ```views/twill/repeaters/accordion_item.blade.php```
```php
  @twillRepeaterTitle('Accordion item')
  @twillRepeaterMax('10') // Optional

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
