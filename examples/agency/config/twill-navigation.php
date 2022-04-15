<?php

return [
    'work' => [
        'title' => 'Work',
        'route' => 'twill.work.sectors.index',
        'primary_navigation' => [
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
];
