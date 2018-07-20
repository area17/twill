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
    'block_preview_render_childs' => true, // indicates if childs should be rendered when using repeater in blocks
    'block_presenter_path' => null, //Allow to set a custom presenter to a block model
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
