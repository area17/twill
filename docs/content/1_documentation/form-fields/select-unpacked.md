# Select Unpacked

![screenshot](/assets/selectunpacked.png)

Form view:
```html
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

Form builder:
```php
Select::make()
    ->name('sectors')
    ->unpack()
    ->options(
        Options::make([
            Option::make('key', 'value'),
            ...
        ])
    );
```


::: details Old method
```php
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
:::

A migration to save the above `select` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->string('discipline')->nullable();
    ...
});
```

When used in a [block](/block-editor/creating-a-block-editor.html), no migration is needed.
