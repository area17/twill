# Select Unpacked

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
