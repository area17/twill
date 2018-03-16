<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit Block Editor configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to provide the package with your configuration
    | for the Block editor service.
    |
     */
    'block_single_layout' => 'site.layouts.block',
    'block_views_path' => 'site.blocks',
    'block_views_mappings' => [],
    'block_preview_render_childs' => true,
    'blocks' => [
        'text' => [
            'title' => 'Body text',
            'icon' => 'text',
            'component' => 'a17-block-wysiwyg',
        ],
        'image' => [
            'title' => 'Image',
            'icon' => 'image',
            'component' => 'a17-block-image',
        ],
    ],
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
];
