---
pageClass: twill-doc
title: Color
---

# Color

![screenshot](/docs/_media/color.png)

```php
@formField('color', [
    'name' => 'main_color',
    'label' => 'Main color'
])
```

| Option  | Description         | Type     | Default value |
| :------ | :------------------ | :------- | :------------ |
| name    | Name of the field   | string   |               |
| label   | Label of the field  | string   |               |


A migration to save a `color` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->string('main_color', 10)->nullable();
    ...
});
```