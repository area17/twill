<?php

namespace A17\Twill;

use A17\Twill\Facades\TwillRoutes as FacadesTwillRoutes;
use A17\Twill\Helpers\Capsule;
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
        $callback = function () use (
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
                        function () use ($routesFile) {
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
        if (($patterns = config('twill.admin_route_patterns')) != null && is_array($patterns)) {
            foreach ($patterns as $label => $pattern) {
                Route::pattern($label, $pattern);
            }
        }
    }

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
        if ($routesFile = $capsule->getRoutesFileIfExists()) {
            $this->registerRoutes(
                $router,
                $this->getRouteGroupOptions(),
                $this->getRouteMiddleware(),
                $this->supportSubdomainRouting(),
                $capsule->getControllersNamespace(),
                $routesFile,
                // When it is not a package capsule we can register it immediately.
                !$capsule->packageCapsule
            );
        }
    }

    public function getModelRouteName(
        string $routeModuleName,
        string $action = '',
        ?string $prefix = null
    ): string {
        $routeModuleName = \A17\Twill\Facades\TwillRoutes::modelToModuleName($routeModuleName);

        // Get the prefix.
        $prefix = $prefix ?? $this->getRoutePrefixForModel($routeModuleName);

        // Create base route name
        $routeName = 'twill.' . ($prefix ? $prefix . '.' : '');

        // Prefix it with module name only if prefix doesn't contains it already
        if (
            config('twill.allow_duplicates_on_route_names', true) ||
            ($prefix !== $routeModuleName &&
                !Str::endsWith($prefix, '.' . $routeModuleName))
        ) {
            $routeName .= (string)($routeModuleName);
        }

        //  Add the action name
        $routeName .= $action ? ".{$action}" : '';

        return $routeName;
    }

    public function getRoutePrefixForModel(string $model): ?string
    {
        $routeModuleName = \A17\Twill\Facades\TwillRoutes::modelToModuleName($model);

        $routes = collect(Route::getRoutes()->getRoutes());

        foreach ($routes as $route) {
            if (
                isset($route->action['prefix'], $route->action['as']) &&
                $route->action['prefix'] !== 'admin' &&
                str_contains($route->action['as'], ".$routeModuleName")
            ) {
                $end = '.' . $routeModuleName . '.index';

                if (str_contains($route->action['as'], $end)) {
                    // Find the string between the requested action.
                    $prefix = Str::between($route->action['as'], 'twill.', $end);

                    break;
                }
            }
        }

        return $prefix ?? null;
    }

    /**
     * Get a model route based on the module class (and backwards compatible module name).
     */
    public function getModelRoute(
        string $routeModuleName,
        string $action = '',
        array $parameters = [],
        bool $absolute = true,
        ?string $prefix = null,
    ): string {
        // Nested module, pass in current parameters for deeply nested modules
        if (Str::contains($routeModuleName, '.')) {
            $parameters = array_merge(Route::current()->parameters(), $parameters);
        }

        return route($this->getModelRouteName($routeModuleName, $action, $prefix), $parameters, $absolute);
    }

    public function modelToModuleName(string $model): string
    {
        return Str::camel(Str::plural(Str::afterLast($model, '\\')));
    }
}
