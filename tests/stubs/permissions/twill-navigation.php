<?php

return [
    'postings' => [
        'title' => 'Postings',
        'module' => true,
    ],
    'settings' => [
        'title' => 'Settings',
        'route' => 'twill.settings',
        'params' => ['section' => 'seo'],
        'primary_navigation' => [
            'seo' => [
                'title' => 'SEO',
                'route' => 'twill.settings',
                'params' => ['section' => 'seo'],
            ],
        ],
    ],
];
