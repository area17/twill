<?php

return [
    'enabled' => [
        'permissions-management' => true,
    ],
    'permissions' => [
        'level' => \A17\Twill\Enums\PermissionLevel::LEVEL_ROLE_GROUP_ITEM,
        'modules' => ['blogs', 'categories'],
    ],
    'block_editor' => [
        'use_twill_blocks' => [],
        'crops' => [
            'blog_image' => [
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
    ],
];
