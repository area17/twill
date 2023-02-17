<?php

namespace A17\Twill\Routing;

use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TwillRegistrar
{
    /**
     * The default actions for a module controller.
     */
    protected array $moduleDefaults = [
        'index',
        'create',
        'store',
        'show',
        'edit',
        'update',
        'destroy',
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

    /**
     * The default actions for a singleton controller.
     *
     * @todo remove unused routes.
     */
    protected array $singletonDefaults = [
        'index',
        'create',
        'store',
        'show',
        'edit',
        'update',
        'destroy',
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
        'editSingleton',
    ];

    public function __construct(
        protected Router $router,
    ) {}

    /**
     * Route a twill module to its controller.
     */
    public function module(string $name, array $options = []): RouteCollection
    {
        $methods = $this->filterMethods($this->moduleDefaults, $options);

        return $this->registerRoutes($name, $methods, $options);
    }

    /**
     * Route a twill singleton to its controller.
     */
    public function singleton(string $name, array $options = []): RouteCollection
    {
        $methods = $this->filterMethods($this->singletonDefaults, $options);

        return $this->registerRoutes($name, $methods, $options);
    }

    /**
     * Filter methods using only and except options.
     */
    protected function filterMethods(array $methods, array $options): array
    {
        if (isset($options['only'])) {
            $methods = array_intersect($methods, (array) $options['only']);
        }

        if (isset($options['except'])) {
            $methods = array_diff($methods, (array) $options['except']);
        }

        return $methods;
    }

    /**
     * Register routes for a twill controller.
     */
    protected function registerRoutes(string $name, array $methods, array $options): RouteCollection
    {
        $prefix = $this->getRouteNamePrefix();
        $controller = $this->getControllerClass($name);
        $resourceDefaults = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

        $collection = new RouteCollection;

        $collection = $this->registerCustomRoutes(
            $name,
            $prefix,
            $controller,
            array_diff($methods, $resourceDefaults),
            $collection,
        );

        $collection = $this->registerResourceRoutes(
            $name,
            $prefix,
            $controller,
            array_intersect($resourceDefaults, $methods),
            $options,
            $collection
        );

        // We only need a single route for the registry to work.
        if ($route = $collection->getRoutes()[0] ?? null) {
            $route->action['twill'] = [
                'name' => $name,
                'prefix' => trim("{$prefix}.{$name}", '.'),
            ];
        }

        return $collection;
    }

    /**
     * Get the controller class for the given name.
     */
    protected function getControllerClass(string $name): string
    {
        return array_reduce(
            explode('.', $name),
            fn ($carry, $slug) => $carry . ucfirst(Str::singular($slug)),
            '',
        ) . 'Controller';
    }

    /**
     * Get a name prefix if necessary or an empty string.
     */
    protected function getRouteNamePrefix(): string
    {
        $groupUri = trim(Str::after(
            $this->router->getLastGroupPrefix(),
            (string) config('twill.admin_app_path'),
        ), '/');

        if (!empty($groupUri)) {
            $groupUriAsName = str_replace('/', '.', $groupUri);

            if (config('twill.allow_duplicates_on_route_names', true)) {
                return $groupUriAsName;
            }

            $groupStack = $this->router->getGroupStack();
            $groupName = $groupStack[array_key_last($groupStack)]['as'] ?? '';

            if (empty($groupName) || !Str::endsWith($groupName, "{$groupUriAsName}.")) {
                return $groupUriAsName;
            }
        }

        return '';
    }

    /**
     * Register laravel resource routes.
     */
    private function registerResourceRoutes(
        string $name,
        string $prefix,
        string $controller,
        array $methods,
        array $options,
        RouteCollection $collection,
    ): RouteCollection {
        Arr::forget($options, ['only', 'except']);

        $this->router->group(
            $prefix ? ['as' => $prefix . '.'] : [],
            function () use ($name, $controller, $methods, $options, $collection) {
                $routes = $this->router
                    ->resource($name, $controller, $options)
                    ->only($methods)
                    ->register()
                    ->getRoutes();
                foreach ($routes as $route) {
                    $collection->add($route);
                }
            }
        );

        return $collection;
    }

    /**
     * Register twill custom routes.
     */
    private function registerCustomRoutes(
        string $name,
        string $prefix,
        string $controller,
        array $methods,
        RouteCollection $collection,
    ): RouteCollection {
        $uri = str_replace('.', '/', $name);

        foreach ($methods as $method) {
            $methodUri = "{$uri}/{$method}";
            $mapping = [
                'as' => ltrim("{$prefix}.{$name}.{$method}", '.'),
                'uses' => "{$controller}@{$method}",
            ];

            if ($method === 'editSingleton') {
                $singularName = Str::singular($name);
                $mapping['as'] = ltrim("{$prefix}.{$singularName}", '.');
                $methodUri = str_replace('.', '/', $singularName);
            }

            $collection->add(match ($method) {
                'browser',
                'tags' => $this->router->get($methodUri, $mapping),
                'restoreRevision' => $this->router->get($methodUri . '/{id}', $mapping),
                'publish',
                'feature',
                'restore',
                'forceDelete' => $this->router->put($methodUri, $mapping),
                'duplicate',
                'preview' => $this->router->put($methodUri . '/{id}', $mapping),
                'reorder',
                'bulkPublish',
                'bulkFeature',
                'bulkDelete',
                'bulkRestore',
                'bulkForceDelete' => $this->router->post($methodUri, $mapping),
                'editSingleton' => $this->router->get($methodUri, $mapping),
            });
        };

        return $collection;
    }
}
