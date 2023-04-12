# Input

![screenshot](/assets/input.png)

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::
:::tab=name.FormBuilder:::

```php
Input::make()
    ->name('subtitle')
    ->label(twillTrans('Subtitle'))
    ->maxLength(100)
    ->required()
    ->note(twillTrans('Field note'))
    ->translatable()
    ->placeholder(twillTrans('Placeholder'))
```

:::#tab:::
:::tab=name.FormView:::

```blade
<x-twill::input 
    name="subtitle"
    label="Subtitle"
    :maxlength="100"
    :required="true"
    note="Hint message goes here"
    placeholder="Placeholder goes here" 
/>

<x-twill::input 
    name="subtitle_translated"
    label="Subtitle Translated"
    :maxlength="100"
    :required="true"
    type="textarea"
    :rows="3"
    :translated="true"
/>
```

:::#tab:::
:::tab=name.Directive:::

```blade
@formField('input', [
    'name' => 'subtitle',
    'label' => 'Subtitle',
    'maxlength' => 100,
    'required' => true,
    'note' => 'Hint message goes here',
    'placeholder' => 'Placeholder goes here',
])

@formField('input', [
    'translated' => true,
    'name' => 'subtitle_translated',
    'label' => 'Subtitle (translated)',
    'maxlength' => 250,
    'required' => true,
    'note' => 'Hint message goes here',
    'placeholder' => 'Placeholder goes here',
    'type' => 'textarea',
    'rows' => 3
])
```

:::#tab:::
:::#tabs:::

| Option      | Description                                                                                                              | Type/values                                                 | Default value |
|:------------|:-------------------------------------------------------------------------------------------------------------------------|:------------------------------------------------------------|:--------------|
| name        | Name of the field                                                                                                        | string                                                      |               |
| label       | Label of the field                                                                                                       | string                                                      |               |
| type        | Type of input field                                                                                                      | text<br/>textarea<br/>email<br/>number<br/>password<br/>url | text          |
| translated  | Defines if the field is translatable                                                                                     | boolean                                                     | false         |
| maxlength   | Max character count of the field                                                                                         | integer                                                     |               |
| note        | Hint message displayed above the field                                                                                   | string                                                      |               |
| placeholder | Text displayed as a placeholder in the field                                                                             | string                                                      |               |
| prefix      | Text displayed as a prefix in the field                                                                                  | string                                                      |               |
| rows        | Sets the number of rows in a textarea                                                                                    | integer                                                     | 5             |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | boolean                                                     | false         |
| disabled    | Disables the field                                                                                                       | boolean                                                     | false         |
| readonly    | Sets the field as readonly                                                                                               | boolean                                                     | false         |
| default     | Sets a default value if empty                                                                                            | string                                                      |               |
| mask        | Set a mask using the alpinejs mask plugin                                                                                | string                                                      |               |

Specific options for the "number" type:

| Option | Description                           | Type/values | Default value |
|:-------|:--------------------------------------|:------------|:--------------|
| min    | Minimum value                         | number      | null          |
| max    | Maximum value                         | number      | null          |
| step   | Step to increment/decrement the value | number      | null          |

A migration to save an `input` field would be:

```php
Schema::table('articles', function (Blueprint $table) {
    ...
    $table->string('subtitle', 100)->nullable();
    ...

});
// OR
Schema::table('article_translations', function (Blueprint $table) {
    ...
    $table->string('subtitle', 250)->nullable();
    ...
});
```

If this `input` field is used for longer strings then the migration would be:

```php
Schema::table('articles', function (Blueprint $table) {
    ...
    $table->text('subtitle')->nullable();
    ...
});
```

When used in a [block](../5_block-editor), no migration is needed.
