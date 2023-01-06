<?php

namespace A17\Twill;

use A17\Twill\Facades\TwillRoutes as FacadesTwillRoutes;
use A17\Twill\Helpers\Capsule;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class TwillRoutes
{
    /**
     * The registry is a key/value store that we can use to easily figure out routes/modules connection later on.
     */
    private array $registry = [];

    public function getModuleRouteFromRegistry(string $module): string
    {
        if (isset($this->registry[$module])) {
            return $this->registry[$module];
        }

        // Find and cache a match.
        /** @var $route \Illuminate\Support\Facades\Route */
        foreach (app('router')->getRoutes()->getRoutes() as $route) {
            if (isset($route->action['twill']['slug']) && $route->action['twill']['slug'] === $module) {
                return $route->action['twill']['customRoutePrefix'];
            }
        }

        return '';
    }

    /**
     * Only useful for testing purposes.
     */
    public function addToRouteRegistry(string $slug, string $prefix): void
    {
        $this->registry[$slug] = $prefix;
    }

    public function buildModuleRoutes(
        string $slug,
        array $options = [],
        array $resource_options = [],
        bool $resource = true
    ): void {
        $slugs = explode('.', $slug);
        $prefixSlug = str_replace('.', '/', $slug);
        Arr::last($slugs);
        $className = implode(
            '',
            array_map(function ($s): string {
                return ucfirst(Str::singular($s));
            }, $slugs)
        );

        $customRoutes = [
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
        $defaults = [
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
                (array)$options['only']
            );
        } elseif (isset($options['except'])) {
            $customRoutes = array_diff(
                $defaults,
                (array)$options['except']
            );
        }

        $lastRouteGroupName = RouteServiceProvider::getLastRouteGroupName();

        $groupPrefix = RouteServiceProvider::getGroupPrefix();

        // Check if name will be a duplicate, and prevent if needed/allowed
        if (RouteServiceProvider::shouldPrefixRouteName($groupPrefix, $lastRouteGroupName)) {
            $customRoutePrefix = "$groupPrefix.$slug";
            $resourceCustomGroupPrefix = "$groupPrefix.";
        } else {
            $customRoutePrefix = $slug;

            // Prevent Laravel from generating route names with duplication
            $resourceCustomGroupPrefix = '';
        }

        $this->addToRouteRegistry($slug, $customRoutePrefix);

        foreach ($customRoutes as $route) {
            $routeSlug = "$prefixSlug/$route";
            $mapping = [
                'as' => $customRoutePrefix . ".$route",
                'uses' => "{$className}Controller@$route",
            ];

            if (in_array($route, ['browser', 'tags'])) {
                $route = Route::get($routeSlug, $mapping);
                $route->action['twill'] = [
                    'customRoutePrefix' => $customRoutePrefix,
                    'slug' => $slug,
                ];
            }

            if ($route === 'restoreRevision') {
                $route = Route::get($routeSlug . '/{id}', $mapping);
                $route->action['twill'] = [
                    'customRoutePrefix' => $customRoutePrefix,
                    'slug' => $slug,
                ];
            }

            if (
                in_array($route, [
                    'publish',
                    'feature',
                    'restore',
                    'forceDelete',
                ])
            ) {
                $route = Route::put($routeSlug, $mapping);
                $route->action['twill'] = [
                    'customRoutePrefix' => $customRoutePrefix,
                    'slug' => $slug,
                ];
            }

            if ($route === 'duplicate' || $route === 'preview') {
                $route = Route::put($routeSlug . '/{id}', $mapping);
                $route->action['twill'] = [
                    'customRoutePrefix' => $customRoutePrefix,
                    'slug' => $slug,
                ];
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
                $route = Route::post($routeSlug, $mapping);
                $route->action['twill'] = [
                    'customRoutePrefix' => $customRoutePrefix,
                    'slug' => $slug,
                ];
            }
        }

        if ($resource) {
            Route::group(
                ['as' => $resourceCustomGroupPrefix],
                function () use ($slug, $className, $resource_options, $customRoutePrefix) {
                    $route = Route::resource(
                        $slug,
                        "{$className}Controller",
                        $resource_options
                    );
                    $route->action['twill'] = [
                        'customRoutePrefix' => $customRoutePrefix,
                        'slug' => $slug,
                    ];
                }
            );
        }
    }

    public function registerRoutes(
        Router $router,
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

                if ($supportSubdomainRouting) {
                    $url = parse_url(config('app.url'));
                    $router->group(
                        $groupOptions + [
                            'domain' => config('twill.admin_app_subdomain', 'admin') .
                                '.{subdomain}.' .
                                $url['host'] ?? config('app.url'),
                        ],
                        $hostRoutes
                    );
                } else {
                    if (config('twill.admin_app_url') || config('twill.admin_app_strict')) {
                        $groupOptions['domain'] = config('twill.admin_app_url') ?? config('app.url');
                    }
                    $router->group(
                        $groupOptions,
                        $hostRoutes
                    );
                }
            }
        };

        if ($instant) {
            // For some reason the afterResolving does not work for the core routes.
            // In other cases it is important to use the afterResolving because the routes are otherwise registered too
            // early.
            $callback();
        } else {
            FacadesTwillRoutes::resolved($callback);
        }
    }

    public function registerRoutePatterns(): void
    {
        if (($patterns = config('twill.admin_route_patterns')) !== null && is_array($patterns)) {
            foreach ($patterns as $label => $pattern) {
                Route::pattern($label, $pattern);
            }
        }
    }

    public function getRouteGroupOptions(): array
    {
        return [
            'as' => config('twill.admin_route_name_prefix', 'twill.'),
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

    public function singleton(
        string $slug,
        array $options = [],
        array $resource_options = [],
        bool $resource = true
    ): void {
        $pluralSlug = Str::plural($slug);
        $modelName = Str::studly($slug);

        $this->module($pluralSlug, $options, $resource_options, $resource);

        $lastRouteGroupName = RouteServiceProvider::getLastRouteGroupName();

        $groupPrefix = RouteServiceProvider::getGroupPrefix();

        // Check if name will be a duplicate, and prevent if needed/allowed
        if (RouteServiceProvider::shouldPrefixRouteName($groupPrefix, $lastRouteGroupName)) {
            $singletonRouteName = "{$groupPrefix}.{$slug}";
        } else {
            $singletonRouteName = $slug;
        }

        Route::get($slug, $modelName . 'Controller@editSingleton')->name($singletonRouteName);
    }

    public function module(
        string $slug,
        array $options = [],
        array $resource_options = [],
        bool $resource = true
    ): void {
        $this->buildModuleRoutes($slug, $options, $resource_options, $resource);
    }

    public function moduleShowWithPreview(
        string $moduleName,
        string $routePrefix = null,
        string $controllerName = null
    ): void {
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
    }
}
