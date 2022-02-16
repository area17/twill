<?php

namespace A17\Twill;

use A17\Twill\Helpers\Capsule;

class TwillRoutes
{
    public function registerRoutes(
        $router,
        $groupOptions,
        $middlewares,
        $supportSubdomainRouting,
        $namespace,
        $routesFile
    ): void {
        if (file_exists($routesFile)) {
            $hostRoutes = function ($router) use (
                $middlewares,
                $namespace,
                $routesFile
            ) {
                $router->group(
                    [
                        'namespace' => $namespace,
                        'middleware' => $middlewares,
                    ],
                    function ($router) use ($routesFile) {
                        require $routesFile;
                    }
                );
            };

            $router->group(
                $groupOptions + [
                    'domain' => config('twill.admin_app_url'),
                ],
                $hostRoutes
            );

            if ($supportSubdomainRouting) {
                $router->group(
                    $groupOptions + [
                        'domain' =>
                            config('twill.admin_app_subdomain', 'admin') .
                            '.{subdomain}.' .
                            config('app.url'),
                    ],
                    $hostRoutes
                );
            }
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
                $capsule->getRoutesFile()
            );
        }
    }
}
