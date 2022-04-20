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
];
