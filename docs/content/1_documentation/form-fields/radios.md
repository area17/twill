# Radios

![screenshot](/assets/radios.png)

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
    ];
@endphp

<x-twill::radios
    name="discipline"
    label="Discipline"
    default="civic"
    :inline="true"
    :options="$options"
/>
```

Form builder:
```php
Radios::make()
    ->name('sectors')
    ->inline()
    ->border()
    ->options(
        Options::make([
            Option::make('key', 'value'),
            ...
        ])
    );
```

::: details Old method
```php
@formField('radios', [
    'name' => 'discipline',
    'label' => 'Discipline',
    'default' => 'civic',
    'inline' => true,
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
    ]
])
```

| Option              | Description                                                                                                              | Type    | Default value                                   |
|:--------------------|:-------------------------------------------------------------------------------------------------------------------------|:--------|:------------------------------------------------|
| name                | Name of the field                                                                                                        | string  |                                                 |
| label               | Label of the field                                                                                                       | string  |                                                 |
| note                | Hint message displayed above the field                                                                                   | string  |                                                 |
| options             | Array of options for the dropdown, must include _value_ and _label_                                                      | array   |                                                 |
| inline              | Defines if the options are displayed on one or multiple lines                                                            | boolean | false                                           |
| default             | Sets a default value                                                                                                     | string  |                                                 |
| requireConfirmation | Displays a confirmation dialog when modifying the field                                                                  | boolean | false                                           |
| confirmTitleText    | The title of the confirmation dialog                                                                                     | string  | 'Confirm selection'                             |
| confirmMessageText  | The text of the confirmation dialog                                                                                      | string  | 'Are you sure you want to change this option ?' |
| required            | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | boolean | false                                           |
| border              | Draws a border around the field                                                                                          | boolean | false                                           |
| columns             | Aligns the options on a grid with a given number of columns                                                              | integer | 0 (off)                                         |
| disabled            | Disables the field                                                                                                       | boolean | false                                           | 
