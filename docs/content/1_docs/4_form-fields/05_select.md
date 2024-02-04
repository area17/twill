# Select

![screenshot](/assets/select.png)

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::
:::tab=name.FormBuilder:::

```php
Select::make()
    ->name('sectors')
    ->options(
        Options::make([
            Option::make('value', 'label'),
            Option::make('value', 'label', selectable: false),
            ...
        ])
    );
```

:::#tab:::
:::tab=name.FormView:::

```blade
@php
    $selectOptions = [
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
    ];
@endphp

<x-twill::select 
    name="office"
    label="office"
    placeholder="Select an office"
    :options="$selectOptions"
/>
```

:::#tab:::
:::tab=name.Directive:::

```blade
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

:::#tab:::
:::#tabs:::

| Option      | Description                                                                                                              | Type/values | Default value |
|:------------|:-------------------------------------------------------------------------------------------------------------------------|:------------|:--------------|
| name        | Name of the field                                                                                                        | string      |               |
| label       | Label of the field                                                                                                       | string      |               |
| options     | Array of options for the dropdown, must include _value_ and _label_                                                      | array       |               |
| unpack      | Defines if the select will be displayed as an open list of options                                                       | boolean     | false         |
| columns     | Aligns the options on a grid with a given number of columns                                                              | integer     | 0 (off)       |
| searchable  | Filter the field values while typing                                                                                     | boolean     | false         |
| note        | Hint message displayed above the field                                                                                   | string      |               |
| placeholder | Text displayed as a placeholder in the field                                                                             | string      |               |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | boolean     | false         |
| default     | 	Sets a default value if empty	      	                                                                                   | string      |               |
| disabled    | Disables the field                                                                                                       | boolean     | false         | 

Select item option
| Option | Description | Type/values | Default value |
| :---------- | :----------------------------------------------------------- | :-------------- | :------------ |
| selectable | Defines if select item should be selectable in the select or not | boolean | true |

Example of `selectable` prop usage:

```php
@php
    $selectOptions = [
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
            'selectable' => false // This item will be non-selectable in the select form component
        ]
    ];
@endphp

<x-twill::select 
    name="office"
    label="office"
    placeholder="Select an office"
    :options="$selectOptions"
/>
```

A migration to save a `select` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->integer('office')->nullable();
    ...
});
```

When used in a [block](../5_block-editor), no migration is needed.

## Select Unpacked

![screenshot](/assets/selectunpacked.png)

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::
:::tab=name.FormBuilder:::

```php
Select::make()
    ->name('sectors')
    ->unpack()
    ->options(
        Options::make([
            Option::make('value', 'label'),
            ...
        ])
    );
```

:::#tab:::
:::tab=name.FormView:::

```blade
@php
    $selectOptions = [
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
    ];
@endphp

<x-twill::select 
    name="office"
    label="office"
    placeholder="Select an office"
    :unpack="true"
    :options="$selectOptions"
/>
```

:::#tab:::
:::tab=name.Directive:::

```blade
@formField('select', [
    'name' => 'office',
    'label' => 'Office',
    'placeholder' => 'Select an office',
    'unpack' => true,
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

:::#tab:::
:::#tabs:::

A migration to save the above `select` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->string('discipline')->nullable();
    ...
});
```

When used in a [block](../5_block-editor), no migration is needed.
