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
    'blocks' => [
        'title' => [
            'title' => 'Title',
            'icon' => 'text',
            'component' => 'a17-block-title',
        ],
        'quote' => [
            'title' => 'Quote',
            'icon' => 'quote',
            'component' => 'a17-block-quote',
        ],
        'text' => [
            'title' => 'Body text',
            'icon' => 'text',
            'component' => 'a17-block-wysiwyg',
        ],
        'image' => [
            'title' => 'Full width Image',
            'icon' => 'image',
            'component' => 'a17-block-image',
        ],
        'sonia' => [
            'title' => 'Sonia',
            'icon' => 'text',
            'component' => 'a17-block-sonia',
        ],
        'charvet' => [
            'title' => 'Charvet',
            'icon' => 'text',
            'component' => 'a17-block-charvet',
        ],
        'grid' => [
            'title' => 'Grid',
            'icon' => 'text',
            'component' => 'a17-block-grid',
        ],
        'complex' => [
            'title' => 'Complex block test',
            'icon' => 'image',
            'component' => 'a17-block-test',
        ],
        'publications' => [
            'title' => 'Publication Grid',
            'icon' => 'text',
            'component' => 'a17-browserfield',
            'attributes' => [
                'max' => 4,
                'itemLabel' => 'Publications',
                'endpoint' => 'https://www.mocky.io/v2/59d77e61120000ce04cb1c5b',
                'modalTitle' => 'Attach publications',
            ],
        ],
    ],
    'crops' => [
        'image' => [
            'default' => [
                [
                    'name' => 'square',
                    'ratio' => 1,
                    'minValues' => [
                        'width' => 100,
                        'height' => 100,
                    ],
                ],
                [
                    'name' => 'landscape',
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
                    'ratio' => 16 / 9,
                    'minValues' => [
                        'width' => 100,
                        'height' => 100,
                    ],
                ],
            ],
            'mobile' => [
                [
                    'name' => 'mobile',
                    'ratio' => 4 / 3,
                    'minValues' => [
                        'width' => 100,
                        'height' => 100,
                    ],
                ],
            ],
        ],
        'block_cover' => [
            'default' => [
                [
                    'name' => 'square',
                    'ratio' => 1,
                    'minValues' => [
                        'width' => 100,
                        'height' => 100,
                    ],
                ],
            ],
        ],
    ],
    'repeaters' => [
        'video' => [
            'title' => 'Video',
            'trigger' => 'Add videos',
            'component' => 'a17-block-test',
            'max' => 4
        ],
        'gridItem' => [
            'title' => 'Grid item',
            'trigger' => 'Add grid item',
            'component' => 'a17-block-video',
            'max' => 4
        ],
        'gridItemMore' => [
            'title' => 'Grid item',
            'trigger' => 'Add grid item',
            'component' => 'a17-block-video',
            'max' => 6
        ]
    ],
    'use_iframes' => false,
    'iframe_wrapper_view' => '',
    'show_render_errors' => env('BLOCK_EDITOR_SHOW_ERRORS', false),
];
