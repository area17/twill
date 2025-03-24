<?php

return [
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
    'repeaters' => [],

    'core_icons' => dirname(__DIR__) . '/frontend/icons',

    'directories' => [
        'source' => [
            'blocks' => [
                [
                    'path' => base_path('vendor/area17/twill/src/Commands/stubs/blocks'),
                    'source' => A17\Twill\Services\Blocks\Block::SOURCE_TWILL,
                ],
                [
                    'path' => resource_path('views/twill/blocks'),
                    'source' => A17\Twill\Services\Blocks\Block::SOURCE_APP,
                ],
            ],

            'repeaters' => [
                [
                    'path' => resource_path('views/twill/repeaters'),
                    'source' => A17\Twill\Services\Blocks\Block::SOURCE_APP,
                ],
                [
                    'path' => base_path('vendor/area17/twill/src/Commands/stubs/repeaters'),
                    'source' => A17\Twill\Services\Blocks\Block::SOURCE_TWILL,
                ],
            ],

            'icons' => [
                dirname(__DIR__) . '/frontend/icons',
                resource_path('views/twill/icons'),
            ],
        ],

        'destination' => [
            'make_dir' => true,

            'blocks' => resource_path('views/twill/blocks'),

            'repeaters' => resource_path('views/twill/repeaters'),
        ],
    ],
    'blocks' => [],
    'block_rules' => [
        // List of block identifiers to globally disallow
        'disable' => [],
        // List of block identifiers to order blocks by if no order is provided
        'order' => [],
    ]
];
