<?php

namespace A17\Twill\Services\Routing;

use A17\Twill\Facades\TwillCapsules;
use A17\Twill\Helpers\Capsule;
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
    protected function getRouteGroupOptions(): array
    {
        $groupOptions = [
            'as' => config('twill.admin_route_name_prefix', 'admin.'),
            'middleware' => [config('twill.admin_middleware_group', 'web')],
            'prefix' => rtrim(ltrim(config('twill.admin_app_path'), '/'), '/'),
        ];

        return $groupOptions;
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

    protected function registerCapsulesRoutes(Router $router): void
    {
        TwillCapsules::getRegisteredCapsules()->each(function (Capsule $capsule) use ($router) {
            $this->registerCapsuleRoutes($router, $capsule);
        });
    }
}
