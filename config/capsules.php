<?php

return [
    'path' => app_path('Twill/Capsules'),

    'namespaces' => [
        'subdir' => '',

        'base' => 'App\Twill\Capsules',

        'models' => 'Models',

        'repositories' => 'Repositories',

        'controllers' => 'Http\Controllers',

        'requests' => 'Http\Requests',
    ],

    'list' => [
        // ['name' => 'Artists', 'enabled' => true],
        // ['name' => 'Posts', 'enabled' => true],
    ],

    'capsule_config_prefix' => 'twill.capsule',
    'capsule_repository_prefix' => env('CAPSULE_REPOSITORY_PREFIX', 'area17')
];

/// To fully override this config:
///
//[
//    'modules' => [
//        'path' => app_path('Twill/Modules'),
//
//        'loaded' => true,
//
//        'list' => [
//            [
//                'name' => 'Posts',
//                'enabled' => true,
//                'module' => 'posts',
//                'plural' => 'Posts',
//                'singular' => 'Post',
//                'namespace' => 'App\Twill\Capsules\Posts',
//                'models' => 'App\Twill\Capsules\Posts\Models',
//                'model' => 'App\Twill\Capsules\Posts\Models\Post',
//                'repositories' => 'App\Twill\Capsules\Posts\Repositories',
//                'controllers' => 'App\Twill\Capsules\Posts\Http\Controllers',
//                'requests' => 'App\Twill\Capsules\Posts\Http\Requests',
//                'psr4_path' =>
//                    '/app-dir/vendor/area17/twill/vendor/orchestra/testbench-core/laravel/app/Twill/Capsules/Posts/app',
//                'root_path' =>
//                    '/app-dir/vendor/area17/twill/vendor/orchestra/testbench-core/laravel/app/Twill/Capsules/Posts',
//                'migrations_dir' =>
//                    '/app-dir/vendor/area17/twill/vendor/orchestra/testbench-core/laravel/app/Twill/Capsules/Posts/database/migrations',
//                'views_dir' =>
//                    '/app-dir/vendor/area17/twill/vendor/orchestra/testbench-core/laravel/app/Twill/Capsules/Posts/resources/views',
//                'view_prefix' => 'Posts.resources.views.admin',
//                'routes_file' =>
//                    '/app-dir/vendor/area17/twill/vendor/orchestra/testbench-core/laravel/app/Twill/Capsules/Posts/routes/twill.php',
//                'models_dir' =>
//                    '/app-dir/vendor/area17/twill/vendor/orchestra/testbench-core/laravel/app/Twill/Capsules/Posts/app/Data/Models',
//                'translation' =>
//                    'App\Twill\Capsules\Posts\Models\PostTranslation',
//                'slug' => 'App\Twill\Capsules\Posts\Models\PostSlug',
//                'revision' =>
//                    'App\Twill\Capsules\Posts\Models\PostRevision',
//                'repository' =>
//                    'App\Twill\Capsules\Posts\Repositories\PostRepository',
//                'repositories_dir' =>
//                    '/app-dir/vendor/area17/twill/vendor/orchestra/testbench-core/laravel/app/Twill/Capsules/Posts/app/Data/Repositories',
//                'controller' =>
//                    'App\Twill\Capsules\Posts\Http\Controllers\PostController',
//                'controllers_dir' =>
//                    '/app-dir/vendor/area17/twill/vendor/orchestra/testbench-core/laravel/app/Twill/Capsules/Posts/app/Http/Controllers',
//                'formRequest' =>
//                    'App\Twill\Capsules\Posts\Http\Requests\PostRequest',
//                'requests_dir' =>
//                    '/app-dir/vendor/area17/twill/vendor/orchestra/testbench-core/laravel/app/Twill/Capsules/Posts/app/Http/Requests',
//            ],
//        ],
//    ],
//];
