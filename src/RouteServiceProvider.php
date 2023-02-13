<?php

namespace A17\Twill;

use A17\Twill\Facades\TwillRoutes;
use A17\Twill\Http\Controllers\Front\GlideController;
use A17\Twill\Http\Middleware\Authenticate;
use A17\Twill\Http\Middleware\Impersonate;
use A17\Twill\Http\Middleware\Localization;
use A17\Twill\Http\Middleware\Permission;
use A17\Twill\Http\Middleware\RedirectIfAuthenticated;
use A17\Twill\Http\Middleware\SupportSubdomainRouting;
use A17\Twill\Http\Middleware\ValidateBackHistory;
use A17\Twill\Services\MediaLibrary\Glide;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'A17\Twill\Http\Controllers';

    protected array $middlewareAliases = [
        'impersonate' => Impersonate::class,
        'localization' => Localization::class,
        'permission' => Permission::class,
        'supportSubdomainRouting' => SupportSubdomainRouting::class,
        'twill_auth' => Authenticate::class,
        'twill_guest' => RedirectIfAuthenticated::class,
        'validateBackHistory' => ValidateBackHistory::class,
    ];

    /**
     * Bootstraps the package services.
     */
    public function boot(): void
    {
        $this->app->bind(TwillRoutes::class);
        $this->registerRouteMiddlewares();
        $this->registerRouteMacros();
        $this->registerRoutePatterns();
        parent::boot();
    }

    public function map(Router $router): void
    {
        $this->mapInternalRoutes($router);

        \A17\Twill\Facades\TwillRoutes::registerRoutes(
            config('twill.namespace', 'App') . '\Http\Controllers\Twill',
            base_path('routes/twill.php'),
            true
        );
    }

    private function mapInternalRoutes(Router $router): void
    {
        $router->group(TwillRoutes::getDefaultOptions() + [
            'namespace' => $this->namespace . '\Admin',
        ], function () use ($router) {
            $router->group(
                ['middleware' => TwillRoutes::getAdminMiddleware()],
                $this->getInternalRouteFile('admin'),
            );
            $router->group(
                [],
                $this->getInternalRouteFile('auth'),
            );
            $router->group(
                $this->app->environment('production') ? ['middleware' => ['twill_auth:twill_users']] : [],
                $this->getInternalRouteFile('templates'),
            );
        });

        if (config('twill.templates_on_frontend_domain')) {
            $router->group([
                'namespace' => $this->namespace . '\Admin',
                'domain' => config('app.url'),
                'middleware' => [
                    config('twill.admin_middleware_group', 'web'),
                    $this->app->environment('production') ? 'twill_auth:twill_users' : null,
                ],
            ], $this->getInternalRouteFile('templates'));
        }

        if (config('twill.media_library.image_service') === Glide::class) {
            $router->get(
                '/' . config('twill.glide.base_path') . '/{path}',
                GlideController::class
            )->where('path', '.*');
        }
    }

    private function getInternalRouteFile(string $name): string
    {
        return __DIR__ . "/../routes/{$name}.php";
    }

    private function registerRouteMiddlewares(): void
    {
        foreach ($this->middlewareAliases as $alias => $class) {
            Route::aliasMiddleware($alias, $class);
        }
    }

    private function registerRoutePatterns(): void
    {
        if (($patterns = config('twill.admin_route_patterns')) !== null && is_array($patterns)) {
            foreach ($patterns as $label => $pattern) {
                Route::pattern($label, $pattern);
            }
        }
    }

    private function registerRouteMacros(): void
    {
        Route::macro('module', function (
            string $slug,
            array $options = [],
            array $resource_options = [],
            bool $resource = true,
        ): void {
            TwillRoutes::module($slug, $options, $resource_options, $resource);
        });

        Route::macro('twillSingleton', function (
            string $slug,
            array $options = [],
            array $resource_options = [],
            bool $resource = true,
        ) {
            TwillRoutes::singleton($slug, $options, $resource_options, $resource);
        });
    }
}
