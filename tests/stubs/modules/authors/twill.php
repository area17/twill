<?php

return [
    'block_editor' => [
        'block_single_layout' => 'site.layouts.block',
        'block_views_path' => 'site.blocks',
        'block_views_mappings' => [],
        'block_preview_render_childs' => true,

        'blocks' => [
            'quote' => [
                'title' => 'Quote',
                'icon' => 'text',
                'component' => 'a17-block-quote',
            ],
        ],

        'crops' => [
            'avatar' => [
                'default' => [
                    [
                        'name' => 'default',
                        'ratio' => 1 / 1,
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
                        'path' => base_path(
                            'vendor/area17/twill/src/Commands/stubs/blocks'
                        ),
                        'source' =>
                            A17\Twill\Services\Blocks\Block::SOURCE_TWILL,
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
                        'path' => base_path(
                            'vendor/area17/twill/src/Commands/stubs/repeaters'
                        ),
                        'source' =>
                            A17\Twill\Services\Blocks\Block::SOURCE_TWILL,
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
