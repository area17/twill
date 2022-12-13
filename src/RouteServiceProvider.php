<?php

namespace A17\Twill;

use A17\Twill\Http\Controllers\Front\GlideController;
use A17\Twill\Http\Middleware\Authenticate;
use A17\Twill\Http\Middleware\Impersonate;
use A17\Twill\Http\Middleware\Localization;
use A17\Twill\Http\Middleware\Permission;
use A17\Twill\Http\Middleware\RedirectIfAuthenticated;
use A17\Twill\Http\Middleware\SupportSubdomainRouting;
use A17\Twill\Http\Middleware\ValidateBackHistory;
use A17\Twill\Services\MediaLibrary\Glide;
use A17\Twill\Facades\TwillRoutes;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'A17\Twill\Http\Controllers';

    /**
     * Bootstraps the package services.
     */
    public function boot(): void
    {
        $this->registerRouteMiddlewares();
        $this->app->bind(TwillRoutes::class);
        $this->registerRouteMacros();
        parent::boot();
    }

    public function map(Router $router): void
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
        $supportSubdomainRouting
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
                if ($supportSubdomainRouting) {
                    $url = parse_url(config('app.url'));
                    $router->group(
                        [
                            'domain' => config('twill.admin_app_subdomain', 'admin') .
                                '.{subdomain}.' .
                                $url['host'] ?? config('app.url'),
                        ],
                        $internalRoutes
                    );
                } else {
                    if (config('twill.admin_app_url') || config('twill.admin_app_strict')) {
                        $options = [
                            'domain' => config('twill.admin_app_url') ?? config('app.url'),
                        ];
                    }
                    $router->group(
                        $options ?? [],
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

        if (config('twill.media_library.image_service') === Glide::class) {
            $router
                ->get(
                    '/' . config('twill.glide.base_path') . '/{path}',
                    GlideController::class
                )
                ->where('path', '.*');
        }
    }

    private function registerRouteMiddlewares(): void
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

    protected function registerRouteMacros(): void
    {
        Route::macro('module', function (
            string $slug,
            array $options = [],
            array $resource_options = [],
            bool $resource = true
        ): void {
            TwillRoutes::module($slug, $options, $resource_options, $resource);
        });

        Route::macro('twillSingleton', function (
            $slug,
            $options = [],
            $resource_options = [],
            $resource = true
        ) {
            TwillRoutes::singleton($slug, $options, $resource_options, $resource);
        });
    }

    public static function shouldPrefixRouteName($groupPrefix, $lastRouteGroupName)
    {
        return !empty($groupPrefix) && (blank($lastRouteGroupName) ||
                config('twill.allow_duplicates_on_route_names', true) ||
                (!Str::endsWith($lastRouteGroupName, ".{$groupPrefix}.")));
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

        if (!empty(config('twill.admin_app_path'))) {
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
