---
pageClass: twill-doc
---

# Multiple Checkboxes

![screenshot](/docs/_media/checkboxes.png)

```php
@formField('checkboxes', [
    'name' => 'sectors',
    'label' => 'Sectors',
    'note' => '3 sectors max & at least 1 sector',
    'min' => 1,
    'max' => 3,
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

| Option  | Description                                                         | Type    | Default value |
| :------ | :------------------------------------------------------------------ | :-------| :------------ |
| name    | Name of the field                                                   | string  |               |
| label   | Label of the field                                                  | string  |               |
| min     | Minimum number of selectable options                                | integer |               |
| max     | Maximum number of selectable options                                | integer |               |
| options | Array of options for the dropdown, must include _value_ and _label_ | array   |               |
| inline  | Defines if the options are displayed on one or multiple lines       | boolean | false         |
| note    | Hint message displayed above the field                              | string  |               |
| border  | Draws a border around the field                                     | boolean | false         |
| columns | Aligns the options on a grid with a given number of columns         | integer | 0 (off)       |
