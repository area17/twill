<?php

namespace A17\Twill;

use A17\Twill\Facades\TwillRoutes as FacadesTwillRoutes;
use A17\Twill\Helpers\Capsule;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class TwillRoutes
{
    public function registerRoutes(
        $router,
        $groupOptions,
        $middlewares,
        $supportSubdomainRouting,
        $namespace,
        $routesFile,
        $instant = false
    ): void {
        $callback = function () use ($router, $groupOptions, $middlewares, $supportSubdomainRouting, $namespace, $routesFile) {
            if (file_exists($routesFile)) {
                $hostRoutes = function (Router $router) use (
                    $middlewares,
                    $namespace,
                    $routesFile
                ) {
                    $router->group(
                        [
                            'namespace' => $namespace,
                            'middleware' => $middlewares,
                        ],
                        function () use ($routesFile) {
                            require $routesFile;
                        }
                    );
                };

                $router->group(
                    $groupOptions + [
                        'domain' => Str::beforeLast(config('twill.admin_app_url'), ':'),
                    ],
                    $hostRoutes
                );

                if ($supportSubdomainRouting) {
                    $router->group(
                        $groupOptions + [
                            'domain' => config('twill.admin_app_subdomain', 'admin') .
                                '.{subdomain}.' .
                                config('app.url'),
                        ],
                        $hostRoutes
                    );
                }
            }
        };

        if ($instant) {
            // For some reasone the afterResolving does not work for the core routes.
            // In other cases it is important to use the afterResolving because the routes are otherwise registered too
            // early.
            $callback();
        } else {
            FacadesTwillRoutes::resolved($callback);
        }
    }

    public function registerRoutePatterns(): void
    {
        if (($patterns = config('twill.admin_route_patterns')) != null) {
            if (is_array($patterns)) {
                foreach ($patterns as $label => $pattern) {
                    Route::pattern($label, $pattern);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getRouteGroupOptions(): array
    {
        return [
            'as' => config('twill.admin_route_name_prefix', 'admin.'),
            'middleware' => [config('twill.admin_middleware_group', 'web')],
            'prefix' => rtrim(ltrim(config('twill.admin_app_path'), '/'), '/'),
        ];
    }

    public function getRouteMiddleware($middleware = null): array
    {
        if (is_array($middleware)) {
            return $middleware;
        }

        $middleware = [
            'twill_auth:twill_users',
            'impersonate',
            'validateBackHistory',
            'localization',
        ];

        if ($this->supportSubdomainRouting()) {
            array_unshift($middleware, 'supportSubdomainRouting');
        }

        return $middleware;
    }

    public function supportSubdomainRouting()
    {
        return config('twill.support_subdomain_admin_routing', false);
    }

    public function registerCapsuleRoutes($router, Capsule $capsule): void
    {
        if ($capsule->routesFileExists()) {
            $this->registerRoutes(
                $router,
                $this->getRouteGroupOptions(),
                $this->getRouteMiddleware(),
                $this->supportSubdomainRouting(),
                $capsule->getControllersNamespace(),
                $capsule->getRoutesFile(),
                // When it is not a package capsule we can register it immediately.
                ! $capsule->packageCapsule
            );
        }
    }
}
