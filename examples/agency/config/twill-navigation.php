<?php

return [
    'homepage' => [
        'title' => 'Homepage',
        'route' => 'twill.featured.homepage',
    ],
    'work' => [
        'title' => 'Work',
        'route' => 'twill.work.works.index',
        'primary_navigation' => [
            'works' => [
                'title' => 'Works',
                'module' => true
            ],
            'sectors' => [
                'title' => 'Sectors',
                'module' => true
            ],
            'disciplines' => [
                'title' => 'Disciplines',
                'module' => true
            ]
        ]
    ],
    'about' => [
        'title' => 'About',
        'route' => 'twill.about.people.index',
        'primary_navigation' => [
            'people' => [
                'title' => 'People',
                'module' => true,
            ],
            'roles' => [
                'title' => 'Roles',
                'module' => true,
            ],
            'about' => [
                'title' => 'Overview',
                'singleton' => true,
            ],
        ]
    ],
    'contact' => [
        'title' => 'Contact',
        'route' => 'twill.contact.offices.index',
        'primary_navigation' => [
            'offices' => [
                'title' => 'Offices',
                'module' => true
            ]
        ]
    ],
    'settings' => [
        'title' => 'Settings',
        'route' => 'twill.settings',
        'params' => ['section' => 'seo'],
        'primary_navigation' => [
            'seo' => [
                'title' => 'SEO',
                'route' => 'twill.settings',
                'params' => ['section' => 'seo']
            ],
            'social' => [
                'title' => 'Social',
                'route' => 'twill.settings',
                'params' => ['section' => 'social']
            ],
        ]
    ]
];
