---
pageClass: twill-doc
---

# Select

![screenshot](/docs/_media/select.png)

```php
@formField('select', [
    'name' => 'office',
    'label' => 'Office',
    'placeholder' => 'Select an office',
    'options' => [
        [
            'value' => 1,
            'label' => 'New York'
        ],
        [
            'value' => 2,
            'label' => 'London'
        ],
        [
            'value' => 3,
            'label' => 'Berlin'
        ]
    ]
])
```

| Option      | Description                                                  | Type/values     | Default value |
| :---------- | :----------------------------------------------------------- | :-------------- | :------------ |
| name        | Name of the field                                            | string          |               |
| label       | Label of the field                                           | string          |               |
| options     | Array of options for the dropdown, must include _value_ and _label_ | array          |               |
| unpack      | Defines if the select will be displayed as an open list of options | true<br/>false  | false         |
| columns     | Aligns the options on a grid with a given number of columns  | integer         | 0 (off)       |
| searchable  | Filter the field values while typing                         | true<br/>false  | false         |
| note        | Hint message displayed above the field                       | string          |               |
| placeholder | Text displayed as a placeholder in the field                 | string          |               |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false  | false         |
| default     |	Sets a default value if empty	      	                       | string          |               |

A migration to save a `select` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->integer('office')->nullable();
    ...
});
```

When used in a [block](/block-editor/creating-a-block-editor.html), no migration is needed.
