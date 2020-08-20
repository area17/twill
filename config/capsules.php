<?php

return [
    'path' => app_path('Twill/Capsules'),

    'list' => [
        // ['name' => 'Posts', 'enabled' => true],
    ],
];

/// To fully override this config:
///
//[
//    'modules' => [
//        'path' => app_path('Twill/Modules'),
//
//        'loaded' = true,
//
//        'list' => [
//            [
//                "name" => "Post",
//                "enabled" => true,
//                "psr4_path" => "/app-path/app/Twill/Modules/Post/app",
//                "namespace" => "App\Twill\Modules\Post",
//                "root_path" => "/app-path/app/Twill/Modules/Post",
//                "migrations_dir" => "/app-path/app/Twill/Modules/Post/database/migrations",
//                "views_dir" => "/app-path/app/Twill/Modules/Post/resources/views",
//                "view_prefix" => "Post.resources.views.admin",
//                "routes_file" => "/app-path/app/Twill/Modules/Post/routes/admin.php",
//                "model" => "App\Twill\Modules\Post\Data\Models\Post",
//                "translation" => "App\Twill\Modules\Post\Data\Models\PostTranslation",
//                "slug" => "App\Twill\Modules\Post\Data\Models\PostSlug",
//                "revision" => "App\Twill\Modules\Post\Data\Models\PostRevision",
//                "repository" => "App\Twill\Modules\Post\Data\Repositories\PostRepository",
//                "controller" => "App\Twill\Modules\Post\Http\Controllers\PostController",
//                "formRequest" => "App\Twill\Modules\Post\Http\Requests\PostRequest",
//            ],
//        ],
//    ];
