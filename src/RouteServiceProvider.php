<?php

namespace A17\Twill;

use A17\Twill\Services\Capsules\Manager;
use A17\Twill\Http\Controllers\Front\GlideController;
use A17\Twill\Http\Middleware\Impersonate;
use A17\Twill\Http\Middleware\Localization;
use A17\Twill\Http\Middleware\RedirectIfAuthenticated;
use A17\Twill\Http\Middleware\SupportSubdomainRouting;
use A17\Twill\Http\Middleware\ValidateBackHistory;
use A17\Twill\Services\Routing\HasRoutes;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    use HasRoutes;

    protected $namespace = 'A17\Twill\Http\Controllers';

    /**
     * Bootstraps the package services.
     *
     * @return void
     */
    public function boot()
    {
        require_once __DIR__ . '/Helpers/routes_helpers.php';
        $this->registerRouteMiddlewares($this->app->get('router'));
        $this->registerMacros();
        parent::boot();
    }

    /**
     * @param Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $this->registerRoutePatterns();

        $this->registerCapsulesRoutes($router);

        $this->mapInternalRoutes(
            $router,
            $this->getRouteGroupOptions(),
            $this->getRouteMiddleware(),
            $this->supportSubdomainRouting()
        );

        $this->mapHostRoutes(
            $router,
            $this->getRouteGroupOptions(),
            $this->getRouteMiddleware(),
            $this->supportSubdomainRouting()
        );
    }

    private function mapHostRoutes(
        $router,
        $groupOptions,
        $middlewares,
        $supportSubdomainRouting,
        $namespace = null
    ) {
        $this->registerRoutes(
            $router,
            $groupOptions,
            $middlewares,
            $supportSubdomainRouting,
            config('twill.namespace', 'App') . '\Http\Controllers\Admin',
            base_path('routes/admin.php')
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
                            'domain' =>
                            config('twill.admin_app_subdomain', 'admin') .
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
        Route::aliasMiddleware(
            'twill_auth',
            \Illuminate\Auth\Middleware\Authenticate::class
        );
        Route::aliasMiddleware('twill_guest', RedirectIfAuthenticated::class);
        Route::aliasMiddleware(
            'validateBackHistory',
            ValidateBackHistory::class
        );
        Route::aliasMiddleware('localization', Localization::class);
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

            $lastRouteGroupName = lastRouteGroupName();

            $groupPrefix = twillRouteGroupPrefix();

            // Check if name will be a duplicate, and prevent if needed/allowed
            if (!empty($groupPrefix) &&
                (
                    blank($lastRouteGroupName) ||
                    config('twill.allow_duplicates_on_route_names', true) ||
                    (!Str::endsWith($lastRouteGroupName, ".{$groupPrefix}."))
                )
            ) {
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

            $lastRouteGroupName = lastRouteGroupName();

            $groupPrefix = twillRouteGroupPrefix();

            // Check if name will be a duplicate, and prevent if needed/allowed
            if (
                !empty($groupPrefix) &&
                (blank($lastRouteGroupName) ||
                    config('twill.allow_duplicates_on_route_names', true) ||
                    (!Str::endsWith($lastRouteGroupName, ".{$groupPrefix}."))
                )
            ) {
                $singletonRouteName = "{$groupPrefix}.{$slug}";
            } else {
                $singletonRouteName = $slug;
            }

            Route::get($slug, $modelName . 'Controller@editSingleton')->name($singletonRouteName);
        });
    }
}
