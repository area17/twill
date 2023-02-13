<?php

namespace A17\Twill\Routing;

use A17\Twill\Helpers\Capsule;
use Illuminate\Container\Container;
use Illuminate\Support\Str;


class TwillRoutes
{
    /**
     * The registry is a key/value store that we can use to easily figure out routes/modules connection later on.
     */
    private array $registry = [];

    public function __construct(
        protected Container $container,
    ) {}

    /**
     * Only useful for testing purposes.
     */
    public function addToRouteRegistry(string $name, string $prefix): void
    {
        $this->registry[$name] = $prefix;
    }

    /**
     * Get the base route name for a module.
     */
    public function getModuleRouteFromRegistry(string $module): string
    {
        return $this->registry[$module]
            ??= $this->getModuleRouteFromRouter($module);
    }

    /**
     * Use the router for when routes are cached.
     */
    private function getModuleRouteFromRouter(string $module): string
    {
        /** @var \Illuminate\Routing\RouteCollection $collection */
        $collection = $this->container->get('router')->getRoutes();
        foreach ($collection->getRoutes() as $route) {
            if (isset($route->action['twill']) && $route->action['twill']['name'] === $module) {
                return $route->action['twill']['prefix'];
            }
        }
        return '';
    }

    /**
     * Route a twill module to its controller.
     *
     * @param array $resource_options Deprecated, use the options instead.
     * @param bool $resource Deprecated, use the options instead.
     */
    public function module(
        string $name,
        array $options = [],
        array $resource_options = [],
        bool $resource = true,
    ): PendingRegistration {
        $registrar = $this->container->make(TwillRegistrar::class);
        $options = $this->handleLegacyParameters($options, $resource_options, $resource);

        return new PendingRegistration('module', $name, $options, $registrar);
    }

    /**
     * Route a twill singleton to its controller.
     *
     * @param array $resource_options Deprecated, use the options instead.
     * @param bool $resource Deprecated, use the options instead.
     */
    public function singleton(
        string $name,
        array $options = [],
        array $resource_options = [],
        bool $resource = true,
    ): PendingRegistration {
        $registrar = $this->container->make(TwillRegistrar::class);
        $options = $this->handleLegacyParameters($options, $resource_options, $resource);

        return new PendingRegistration('singleton', Str::plural($name), $options, $registrar);
    }

    /**
     * Merge legacy parameters into the options array.
     *
     * Adds resource routes to the except list when resource is false and
     * prevent them from being ignored when using the only option.
     */
    private function handleLegacyParameters(
        array $options = [],
        array $resource_options = [],
        bool $resource = true,
    ): array {
        $resourceDefaults = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

        if (!$resource) {
            $options['except'] = [...(array) $options['except'] ?? [], ...$resourceDefaults];
        }

        if (isset($options['only']) && !isset($resource_options['only'])) {
            $options['only'] = [...(array) $options['only'], ...$resourceDefaults];
        }

        return array_merge_recursive($resource_options, $options);
    }

    /**
     * Register twill preview routes.
     */
    public function moduleShowWithPreview(
        string $moduleName,
        string $routePrefix = null,
        string $controllerName = null,
    ): void {
        $routePrefix ??= $moduleName;
        $controllerName ??= ucfirst(Str::plural($moduleName));
        $routePrefix = Str::start(Str::finish($routePrefix, '/'), '/');

        $this->container->get('router')->get(
            $routePrefix . '{slug}',
            $controllerName . 'Controller@show'
        )->name($moduleName . '.show');

        $this->container->get('router')->get(
            '/admin-preview' . $routePrefix . '{slug}',
            $controllerName . 'Controller@show'
        )->middleware([
            'web',
            'twill_auth:twill_users',
            'can:list'
        ])->name($moduleName . '.preview');
    }

    /**
     * Register routes inside twill.
     */
    public function registerRoutes(string $namespace, string $routesFile, bool $instant = false): void
    {
        $callback = function () use ($namespace, $routesFile) {
            if (file_exists($routesFile)) {
                $this->container->get('router')->group(
                    array_merge_recursive($this->getDefaultOptions(), [
                        'namespace' => $namespace,
                        'middleware' => $this->getAdminMiddleware(),
                    ]),
                    $routesFile,
                );
            }
        };

        if ($instant) {
            // For some reason the afterResolving does not work for the core routes.
            // In other cases it is important to use the afterResolving because the routes are otherwise registered too
            // early.
            $callback();
        } else {
            \A17\Twill\Facades\TwillRoutes::resolved($callback);
        }
    }

    /**
     * Register capsule routes inside twill.
     */
    public function registerCapsuleRoutes(Capsule $capsule): void
    {
        if ($routesFile = $capsule->getRoutesFileIfExists()) {
            $this->registerRoutes(
                $capsule->getControllersNamespace(),
                $routesFile,
                // When it is not a package capsule we can register it immediately.
                !$capsule->packageCapsule
            );
        }
    }

    /**
     * Get middleware used by admin twill routes.
     */
    public function getAdminMiddleware($middleware = null): array
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

        if (config('twill.support_subdomain_admin_routing', false)) {
            array_unshift($middleware, 'supportSubdomainRouting');
        }

        return $middleware;
    }

    /**
     * Get options used by all twill routes.
     */
    public function getDefaultOptions(): array
    {
        $options = [
            'as' => config('twill.admin_route_name_prefix', 'twill.'),
            'middleware' => [config('twill.admin_middleware_group', 'web')],
            'prefix' => rtrim(ltrim(config('twill.admin_app_path'), '/'), '/'),
        ];

        if (config('twill.support_subdomain_admin_routing', false)) {
            $options['domain'] = config('twill.admin_app_subdomain', 'admin')
                . '.{subdomain}.'
                . parse_url(config('app.url'), PHP_URL_HOST) ?? config('app.url');
        } elseif (config('twill.admin_app_url') || config('twill.admin_app_strict')) {
            $options['domain'] = config('twill.admin_app_url') ?? config('app.url');
        }

        return $options;
    }
}
