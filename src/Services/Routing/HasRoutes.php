<?php

namespace A17\Twill\Services\Routing;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use A17\Twill\Services\Capsules\Manager;

trait HasRoutes
{
    protected function registerRoutes(
        $router,
        $groupOptions,
        $middlewares,
        $supportSubdomainRouting,
        $namespace,
        $routesFile
    ) {
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

    public function registerRoutePatterns()
    {
        if (($patterns = config('twill.admin_route_patterns')) != null && is_array($patterns)) {
            foreach ($patterns as $label => $pattern) {
                Route::pattern($label, $pattern);
            }
        }
    }

    protected function getRouteGroupOptions(): array
    {
        return [
            'as' => config('twill.admin_route_name_prefix', 'twill.'),
            'middleware' => [config('twill.admin_middleware_group', 'web')],
            'prefix' => rtrim(ltrim(config('twill.admin_app_path'), '/'), '/'),
        ];
    }

    public function getRouteMiddleware($middleware = null)
    {
        if (is_array($middleware)) {
            return $middleware;
        }

        $middleware = [
            'twill_auth:twill_users',
            'impersonate',
            'validateBackHistory',
            'localization',
            'permission',
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

    public function registerCapsuleRoutes($router, $capsule, $manager)
    {
        if (file_exists($capsule['routes_file'])) {
            $this->registerRoutes(
                $router,
                $this->getRouteGroupOptions(),
                $this->getRouteMiddleware(),
                $this->supportSubdomainRouting(),
                $manager->capsuleNamespace(
                    $capsule['name'],
                    'controllers'
                ),
                $capsule['routes_file']
            );
        }
    }

    protected function registerCapsulesRoutes(Router $router)
    {
        $manager = (new Manager());

        $manager->getCapsuleList()
                ->each(function ($capsule) use ($router, $manager) {
                    $this->registerCapsuleRoutes($router, $capsule, $manager);
                });
    }
}
