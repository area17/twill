---
pageClass: twill-doc
---

# Default Configuration

```php
// config/twill.php

return [
    'block_editor' => [
        /*
        |--------------------------------------------------------------------------
        | Twill Block Editor configuration
        |--------------------------------------------------------------------------
        |
        | This array allows you to provide the package with your configuration
        | for the Block editor field and Editor features.
        |
        */
        'block_single_layout' => 'site.layouts.block', // layout to use when rendering a single block in the editor
        'block_views_path' => 'site.blocks', // path where a view file per block type is stored
        'block_views_mappings' => [], // custom mapping of block types and views
        'block_preview_render_childs' => true, // indicates if childs should be rendered when using repeater in blocks
        'block_presenter_path' => null, // allow to set a custom presenter to a block model
        // Indicates if blocks templates should be inlined in HTML.
        // When setting to false, make sure to build Twill with your all your custom blocks.
        'inline_blocks_templates' => true,
        'custom_vue_blocks_resource_path' => 'assets/js/blocks',
        'use_twill_blocks' => ['text', 'image'],
        'crops' => [
            'image' => [
                'desktop' => [
                    [
                        'name' => 'desktop',
                        'ratio' => 16 / 9,
                        'minValues' => [
                            'width' => 100,
                            'height' => 100,
                        ],
                    ],
                ],
                'tablet' => [
                    [
                        'name' => 'tablet',
                        'ratio' => 4 / 3,
                        'minValues' => [
                            'width' => 100,
                            'height' => 100,
                        ],
                    ],
                ],
                'mobile' => [
                    [
                        'name' => 'mobile',
                        'ratio' => 1,
                        'minValues' => [
                            'width' => 100,
                            'height' => 100,
                        ],
                    ],
                ],
            ],
        ],
        'directories' => [
            'source' => [
                'blocks' => [
                    [
                        'path' => base_path('vendor/area17/twill/src/Commands/stubs/blocks'),
                        'source' => A17\Twill\Services\Blocks\Block::SOURCE_TWILL,
                    ],
                    [
                        'path' => resource_path('views/admin/blocks'),
                        'source' => A17\Twill\Services\Blocks\Block::SOURCE_APP,
                    ],
                ],
                'repeaters' => [
                    [
                        'path' => resource_path('views/admin/repeaters'),
                        'source' => A17\Twill\Services\Blocks\Block::SOURCE_APP,
                    ],
                    [
                        'path' => base_path('vendor/area17/twill/src/Commands/stubs/repeaters'),
                        'source' => A17\Twill\Services\Blocks\Block::SOURCE_TWILL,
                    ],
                ],
                'icons' => [
                    base_path('vendor/area17/twill/frontend/icons'),
                    resource_path('views/admin/icons'),
                ],
            ],
            'destination' => [
                'make_dir' => true,
                'blocks' => resource_path('views/admin/blocks'),
                'repeaters' => resource_path('views/admin/repeaters'),
            ],
        ],
    ],
];
```
