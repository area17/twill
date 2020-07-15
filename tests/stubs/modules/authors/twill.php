<?php

return [
    'block_editor' => [
        'block_single_layout' => 'site.layouts.block',
        'block_views_path' => 'site.blocks',
        'block_views_mappings' => [],
        'block_preview_render_childs' => true,

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
    'dashboard' => [
        'modules' => [
            App\Models\Author::class => [
                'name' => 'authors', // module name
                'label' => 'authors', // optional, if the name of your module above does not work as a label
                'label_singular' => 'author', // optional, if the automated singular version of your name/label above does not work as a label
                'routePrefix' => 'personnel', // optional, if the module is living under a specific routes group
                'count' => true, // show total count with link to index of this module
                'create' => true, // show link in create new dropdown
                'activity' => true, // show activities on this module in actities list
                'draft' => true, // show drafts of this module for current user
                'search' => true, // show results for this module in global search
                'search_fields' => ['name'],
            ],

            App\Models\Category::class => [
                'name' => 'categories', // module name
                'label' => 'categories', // optional, if the name of your module above does not work as a label
                'label_singular' => 'category', // optional, if the automated singular version of your name/label above does not work as a label
                'routePrefix' => '', // optional, if the module is living under a specific routes group
                'count' => true, // show total count with link to index of this module
                'create' => true, // show link in create new dropdown
                'activity' => true, // show activities on this module in actities list
                'draft' => true, // show drafts of this module for current user
                'search' => true, // show results for this module in global search
            ],
        ],

        'analytics' => ['enabled' => false],

        'search_endpoint' => 'admin.search',
    ],
];
