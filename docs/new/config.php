<?php

return [
    'production' => false,
    'baseUrl' => '',
    'title' => 'Twill',
    'description' => 'Twill documentation',
    'collections' => [],
    'navigation' => [
        'Blog' => [
            'children' => [
                'What is new in Twill 3' => 'blog/what-is-new-in-twill-3',
            ],
        ],
        'Guides' => [
            'children' => [
                'Page builder with blade' => [
                    'url' => 'guides/page-builder-with-blade',
                    'children' => [
                        'Intro' => 'guides/page-builder-with-blade',
                        'Installing Laravel' => 'guides/page-builder-with-blade/installing-laravel',
                        'Installing Twill' => 'guides/page-builder-with-blade/installing-twill',
                        'Creating the page module' => 'guides/page-builder-with-blade/creating-the-page-module',
                        'Configuring the page module' => 'guides/page-builder-with-blade/configuring-the-page-module',
                        'Creating a block' => 'guides/page-builder-with-blade/creating-a-block',
                        'Fixing the preview' => 'guides/page-builder-with-blade/fixing-the-preview',
                    ],
                ],
            ],
        ],
    ],
];
