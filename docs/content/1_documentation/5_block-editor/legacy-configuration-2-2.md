# Legacy Configuration (< 2.2)

#### Twill prior to version 2.2

For Twill version 2.1.x and below, in the `config/twill.php` `block_editor` array, define all *blocks* and *repeaters* available in your project, including the block title, the icon used when displaying it in the block editor form and the associated component name. It would look like this:

filename: ```config/twill.php```
```php
    'block_editor' => [
        'blocks' => [
            ...
            'quote' => [
                'title' => 'Quote',
                'icon' => 'text',
                'component' => 'a17-block-quote',
            ],
            'media' => [
                'title' => 'Media',
                'icon' => 'image',
                'component' => 'a17-block-media',
            ],
            'accordion' => [
                'title' => 'Accordion',
                'icon' => 'text',
                'component' => 'a17-block-accordion',
            ],
            ...
        ]
        'repeaters' => [
            'accordion_item' => [
                'title' => 'Accordion item',
                'icon' => 'text',
                'component' => 'a17-block-accordion_item',
            ],
            ...
        ],
    ],
```

**Please note the naming convention. If the *block* added is `quote` then the component should be prefixed with `a17-block-`.**

If you added a block named *awesome_block*, your configuration would look like this:

```php
    'block_editor' => [
        'blocks' => [
            ...
            'awesome_block' => [
                'title' => 'Title for the awesome block',
                'icon' => 'text',
                'component' => 'a17-block-awesome_block',
            ],
            ..
        ]
```

##### Common errors
- If you add the *container* block to the _repeaters_ section inside the config, it won't work, e.g.:
```php
        'repeaters' => [
            ...
            'accordion' => [
                'title' => 'Accordion',
                'trigger' => 'Add accordion',
                'component' => 'a17-block-accordion',
                'max' => 10,
            ],
            ...
        ]
```

- If you use a different name for the block inside the _repeaters_ section, it also won't work, e. g.:
```php
        'repeaters' => [
            ...
            'accordion-item' => [
                'title' => 'Accordion',
                'trigger' => 'Add accordion',
                'component' => 'a17-block-accordion_item',
                'max' => 10,
            ],
            ...
        ]
```

- Not adding the *item* block to the _repeaters_ section will also result in failure.
