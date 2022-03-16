<?php

namespace A17\Twill;

use A17\Twill\Http\Controllers\Front\GlideController;
use A17\Twill\Http\Middleware\Authenticate;
use A17\Twill\Http\Middleware\Impersonate;
use A17\Twill\Http\Middleware\Localization;
use A17\Twill\Http\Middleware\RedirectIfAuthenticated;
use A17\Twill\Http\Middleware\SupportSubdomainRouting;
use A17\Twill\Http\Middleware\ValidateBackHistory;
use A17\Twill\Http\Middleware\Permission;
use A17\Twill\Http\Middleware\Authenticate;
use A17\Twill\Services\Routing\HasRoutes;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'A17\Twill\Http\Controllers';

    /**
     * Bootstraps the package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMacros();
        $this->registerRouteMiddlewares($this->app->get('router'));
        $this->app->bind(TwillRoutes::class);
        parent::boot();
    }

    /**
     * @param Router $router
     * @return void
     */
    public function map(Router $router)
    {
        \A17\Twill\Facades\TwillRoutes::registerRoutePatterns();

        $this->mapInternalRoutes(
            $router,
            \A17\Twill\Facades\TwillRoutes::getRouteGroupOptions(),
            \A17\Twill\Facades\TwillRoutes::getRouteMiddleware(),
            \A17\Twill\Facades\TwillRoutes::supportSubdomainRouting()
        );

        $this->mapHostRoutes(
            $router,
            \A17\Twill\Facades\TwillRoutes::getRouteGroupOptions(),
            \A17\Twill\Facades\TwillRoutes::getRouteMiddleware(),
            \A17\Twill\Facades\TwillRoutes::supportSubdomainRouting()
        );
    }

    private function mapHostRoutes(
        $router,
        $groupOptions,
        $middlewares,
        $supportSubdomainRouting,
        $namespace = null
    ) {
        \A17\Twill\Facades\TwillRoutes::registerRoutes(
            $router,
            $groupOptions,
            $middlewares,
            $supportSubdomainRouting,
            config('twill.namespace', 'App') . '\Http\Controllers\Twill',
            base_path('routes/twill.php'),
            true
        );
    }

    private function mapInternalRoutes(
        $router,
        $groupOptions,
        $middlewares,
        $supportSubdomainRouting
    ) {
        $internalRoutes = function ($router) use (
            $middlewares,
            $supportSubdomainRouting
        ) {
            $router->group(['middleware' => $middlewares], function ($router) {
                require __DIR__ . '/../routes/admin.php';
            });

            $router->group(
                [
                    'middleware' => $supportSubdomainRouting
                    ? ['supportSubdomainRouting']
                    : [],
                ],
                function ($router) {
                    require __DIR__ . '/../routes/auth.php';
                }
            );

            $router->group(
                [
                    'middleware' => $this->app->environment('production')
                    ? ['twill_auth:twill_users']
                    : [],
                ],
                function ($router) {
                    require __DIR__ . '/../routes/templates.php';
                }
            );
        };

        $router->group(
            $groupOptions + [
                'namespace' => $this->namespace . '\Admin',
            ],
            function ($router) use ($internalRoutes, $supportSubdomainRouting) {
                $router->group(
                    [
                        'domain' => config('twill.admin_app_url'),
                    ],
                    $internalRoutes
                );

                if ($supportSubdomainRouting) {
                    $router->group(
                        [
                            'domain' => config('twill.admin_app_subdomain', 'admin') .
                            '.{subdomain}.' .
                            config('app.url'),
                        ],
                        $internalRoutes
                    );
                }
            }
        );

        if (config('twill.templates_on_frontend_domain')) {
            $router->group(
                [
                    'namespace' => $this->namespace . '\Admin',
                    'domain' => config('app.url'),
                    'middleware' => [
                        config('twill.admin_middleware_group', 'web'),
                    ],
                ],
                function ($router) {
                    $router->group(
                        [
                            'middleware' => $this->app->environment(
                                'production'
                            )
                            ? ['twill_auth:twill_users']
                            : [],
                        ],
                        function ($router) {
                            require __DIR__ . '/../routes/templates.php';
                        }
                    );
                }
            );
        }

        if (
            config('twill.media_library.image_service') ===
            'A17\Twill\Services\MediaLibrary\Glide'
        ) {
            $router
                ->get(
                    '/' . config('twill.glide.base_path') . '/{path}',
                    GlideController::class
                )
                ->where('path', '.*');
        }
    }

    /**
     * Register Route middleware.
     *
     * @param Router $router
     * @return void
     */
    private function registerRouteMiddlewares(Router $router)
    {
        Route::aliasMiddleware(
            'supportSubdomainRouting',
            SupportSubdomainRouting::class
        );
        Route::aliasMiddleware('impersonate', Impersonate::class);
        Route::aliasMiddleware('twill_auth', Authenticate::class);
        Route::aliasMiddleware('twill_guest', RedirectIfAuthenticated::class);
        Route::aliasMiddleware(
            'validateBackHistory',
            ValidateBackHistory::class
        );
        Route::aliasMiddleware('localization', Localization::class);
        Route::aliasMiddleware('permission', Permission::class);
    }

    /**
     * Registers Route macros.
     *
     * @return void
     */
    protected function registerMacros()
    {
        Route::macro('moduleShowWithPreview', function (
            $moduleName,
            $routePrefix = null,
            $controllerName = null
        ) {
            if ($routePrefix === null) {
                $routePrefix = $moduleName;
            }

            if ($controllerName === null) {
                $controllerName = ucfirst(Str::plural($moduleName));
            }

            $routePrefix = empty($routePrefix)
            ? '/'
            : (Str::startsWith($routePrefix, '/')
                ? $routePrefix
                : '/' . $routePrefix);
            $routePrefix = Str::endsWith($routePrefix, '/')
            ? $routePrefix
            : $routePrefix . '/';

            Route::name($moduleName . '.show')->get(
                $routePrefix . '{slug}',
                $controllerName . 'Controller@show'
            );
            Route::name($moduleName . '.preview')
                ->get(
                    '/admin-preview' . $routePrefix . '{slug}',
                    $controllerName . 'Controller@show'
                )
                ->middleware(['web', 'twill_auth:twill_users', 'can:list']);
        });

        Route::macro('module', function (
            $slug,
            $options = [],
            $resource_options = [],
            $resource = true
        ) {
            $slugs = explode('.', $slug);
            $prefixSlug = str_replace('.', '/', $slug);
            $_slug = Arr::last($slugs);
            $className = implode(
                '',
                array_map(function ($s) {
                    return ucfirst(Str::singular($s));
                }, $slugs)
            );

            $customRoutes = $defaults = [
                'reorder',
                'publish',
                'bulkPublish',
                'browser',
                'feature',
                'bulkFeature',
                'tags',
                'preview',
                'restore',
                'bulkRestore',
                'forceDelete',
                'bulkForceDelete',
                'bulkDelete',
                'restoreRevision',
                'duplicate',
            ];

            if (isset($options['only'])) {
                $customRoutes = array_intersect(
                    $defaults,
                    (array) $options['only']
                );
            } elseif (isset($options['except'])) {
                $customRoutes = array_diff(
                    $defaults,
                    (array) $options['except']
                );
            }

            $lastRouteGroupName = RouteServiceProvider::getLastRouteGroupName();

            $groupPrefix = RouteServiceProvider::getGroupPrefix();

            // Check if name will be a duplicate, and prevent if needed/allowed
            if (RouteServiceProvider::shouldPrefixRouteName($groupPrefix, $lastRouteGroupName)) {
                $customRoutePrefix = "{$groupPrefix}.{$slug}";
                $resourceCustomGroupPrefix = "{$groupPrefix}.";
            } else {
                $customRoutePrefix = $slug;

                // Prevent Laravel from generating route names with duplication
                $resourceCustomGroupPrefix = '';
            }

            foreach ($customRoutes as $route) {
                $routeSlug = "{$prefixSlug}/{$route}";
                $mapping = [
                    'as' => $customRoutePrefix . ".{$route}",
                    'uses' => "{$className}Controller@{$route}",
                ];

                if (in_array($route, ['browser', 'tags'])) {
                    Route::get($routeSlug, $mapping);
                }

                if (in_array($route, ['restoreRevision'])) {
                    Route::get($routeSlug . '/{id}', $mapping);
                }

                if (
                    in_array($route, [
                        'publish',
                        'feature',
                        'restore',
                        'forceDelete',
                    ])
                ) {
                    Route::put($routeSlug, $mapping);
                }

                if (in_array($route, ['duplicate'])) {
                    Route::put($routeSlug . '/{id}', $mapping);
                }

                if (in_array($route, ['preview'])) {
                    Route::put($routeSlug . '/{id}', $mapping);
                }

                if (
                    in_array($route, [
                        'reorder',
                        'bulkPublish',
                        'bulkFeature',
                        'bulkDelete',
                        'bulkRestore',
                        'bulkForceDelete',
                    ])
                ) {
                    Route::post($routeSlug, $mapping);
                }
            }

            if ($resource) {
                Route::group(
                    ['as' => $resourceCustomGroupPrefix],
                    function () use ($slug, $className, $resource_options) {
                        Route::resource(
                            $slug,
                            "{$className}Controller",
                            $resource_options
                        );
                    }
                );
            }
        });

        Route::macro('singleton', function (
            $slug,
            $options = [],
            $resource_options = [],
            $resource = true
        ) {
            $pluralSlug = Str::plural($slug);
            $modelName = Str::studly($slug);

            Route::module($pluralSlug, $options, $resource_options, $resource);

            $lastRouteGroupName = RouteServiceProvider::getLastRouteGroupName();

            $groupPrefix = RouteServiceProvider::getGroupPrefix();

            // Check if name will be a duplicate, and prevent if needed/allowed
            if (RouteServiceProvider::shouldPrefixRouteName($groupPrefix, $lastRouteGroupName)) {
                $singletonRouteName = "{$groupPrefix}.{$slug}";
            } else {
                $singletonRouteName = $slug;
            }

            Route::get($slug, $modelName . 'Controller@editSingleton')->name($singletonRouteName);
        });
    }

    public static function shouldPrefixRouteName($groupPrefix, $lastRouteGroupName)
    {
        return ! empty($groupPrefix) && (blank($lastRouteGroupName) ||
            config('twill.allow_duplicates_on_route_names', true) ||
            (! Str::endsWith($lastRouteGroupName, ".{$groupPrefix}.")));
    }

    public static function getLastRouteGroupName()
    {
        // Get the current route groups
        $routeGroups = Route::getGroupStack() ?? [];

        // Get the name prefix of the last group
        return end($routeGroups)['as'] ?? '';
    }

    public static function getGroupPrefix()
    {
        $groupPrefix = trim(
            str_replace('/', '.', Route::getLastGroupPrefix()),
            '.'
        );

        if (! empty(config('twill.admin_app_path'))) {
            $groupPrefix = ltrim(
                str_replace(
                    config('twill.admin_app_path'),
                    '',
                    $groupPrefix
                ),
                '.'
            );
        }

        return $groupPrefix;
    }
}
