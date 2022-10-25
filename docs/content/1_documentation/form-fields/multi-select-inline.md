# Multi Select Inline

![screenshot](/assets/multiselectinline.png)

Form view:
```html
@php
    $options = [
        [
            'value' => 'arts',
            'label' => 'Arts & Culture'
        ],
        [
            'value' => 'finance',
            'label' => 'Banking & Finance'
        ],
        [
            'value' => 'civic',
            'label' => 'Civic & Public'
        ],
        [
            'value' => 'design',
            'label' => 'Design & Architecture'
        ],
        [
            'value' => 'education',
            'label' => 'Education'
        ]
    ];
@endphp

<x-twill::multi-select
    name="sectors"
    label="Sectors"
    :unpack="false"
    :options="$options"
/>
```

Form builder:
```php
MultiSelect::make()
    ->inline()
    ->options(
        Options::make([
            Option::make('key', 'value'),
            ...
        ])
    );
```


::: details Old method
```php
@formField('multi_select', [
    'name' => 'sectors',
    'label' => 'Sectors',
    'unpack' => false,
    'options' => [
        [
            'value' => 'arts',
            'label' => 'Arts & Culture'
        ],
        [
            'value' => 'finance',
            'label' => 'Banking & Finance'
        ],
        [
            'value' => 'civic',
            'label' => 'Civic & Public'
        ],
        [
            'value' => 'design',
            'label' => 'Design & Architecture'
        ],
        [
            'value' => 'education',
            'label' => 'Education'
        ]
    ]
])
```
:::

See [Multi select](/form-fields/multi-select.html) for more information on how to implement the field with static and dynamic values.
