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
            'attributes' => [
                'cropContext' => 'cover',
            ],
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
    'use_iframes' => false,
    'iframe_wrapper_view' => '',
    'show_render_errors' => env('BLOCK_EDITOR_SHOW_ERRORS', false),
];
