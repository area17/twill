<?php

return [
    'buckets' => [
        'homepage' => [
            'name' => 'Home',
            'buckets' => [
                'home_main_feature' => [
                    'name' => 'Home main feature (carousel)',
                    'bucketables' => [
                        [
                            'module' => 'works',
                            'name' => 'Works',
                            'scopes' => ['published' => true],
                        ],
                    ],
                    'max_items' => 7,
                ],
                'home_secondary_features' => [
                    'name' => 'Home secondary features',
                    'bucketables' => [
                        [
                            'module' => 'works',
                            'name' => 'Works',
                            'scopes' => ['published' => true],
                        ],
                    ],
                    'max_items' => 100,
                ],
            ],
        ],
    ],
    'dashboard' => [
        'modules' => [
            'works' => [
                'name' => 'works',
                'label' => 'works',
                'label_singular' => 'work',
                'routePrefix' => 'work',
                'count' => true,
                'create' => true,
                'activity' => true,
                'draft' => true,
                'search' => true,
            ],
            'people' => [
                'name' => 'people',
                'label' => 'people',
                'label_singular' => 'person',
                'routePrefix' => 'about',
                'count' => true,
                'create' => true,
                'activity' => true,
                'draft' => true,
                'search' => true,
                'search_fields' => ['full_name']
            ],
            'offices' => [
                'name' => 'offices',
                'label' => 'offices',
                'label_singular' => 'office',
                'routePrefix' => 'contact',
                'count' => true,
                'create' => true,
                'activity' => true,
                'draft' => true,
                'search' => true,
            ],
        ],
    ],
];
