# Color

<img style="width:50%; margin:32px auto;" src="/assets/color.png" />

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::
:::tab=name.FormBuilder:::

```php
Color::make()
    ->name('featured')
```

:::#tab:::
:::tab=name.FormView:::

```blade
<x-twill::color
    name="main_color"
    label="Main color"
/>
```

:::#tab:::
:::tab=name.Directive:::

```blade
@formField('color', [
    'name' => 'main_color',
    'label' => 'Main color'
])
```

:::#tab:::
:::#tabs:::

| Option  | Description        | Type   | Default value |
|:--------|:-------------------|:-------|:--------------|
| name    | Name of the field  | string |               |
| label   | Label of the field | string |               |
| default | The default value  | string |               |

A migration to save a `color` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->string('main_color', 10)->nullable();
    ...
});
```
