<?php

namespace A17\CmsToolkit;

use A17\CmsToolkit\Http\Middleware\Impersonate;
use A17\CmsToolkit\Http\Middleware\NoDebugBar;
use A17\CmsToolkit\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'A17\CmsToolkit\Http\Controllers';

    public function boot()
    {
        $this->registerRouteMiddlewares();
        $this->registerMacros();
        parent::boot();
    }

    public function map(Router $router)
    {
        $router->group([
            'namespace' => config('cms-toolkit.namespace', 'App') . '\Http\Controllers\Admin',
            'domain' => config('cms-toolkit.admin_app_url'),
            'as' => 'admin.',
            'middleware' => [config('cms-toolkit.admin_middleware_group', 'web')],
        ], function ($router) {
            $router->group(['middleware' => ['auth', 'impersonate']], function ($router) {
                require base_path('routes/admin.php');
            });
        });

        $router->group([
            'namespace' => $this->namespace . '\Admin',
            'domain' => config('cms-toolkit.admin_app_url'),
            'as' => 'admin.',
            'middleware' => [config('cms-toolkit.admin_middleware_group', 'web')],
        ],
            function ($router) {
                $router->group(['middleware' => ['auth', 'impersonate']], function ($router) {
                    require __DIR__ . '/../routes/admin.php';
                });

                $router->group(['middleware' => ['noDebugBar']], function ($router) {
                    require __DIR__ . '/../routes/auth.php';
                    require __DIR__ . '/../routes/templates.php';
                });
            }
        );
    }

    private function registerRouteMiddlewares()
    {
        Route::middleware('noDebugBar', NoDebugBar::class);
        Route::middleware('impersonate', Impersonate::class);
        Route::middleware('guest', RedirectIfAuthenticated::class);
    }

    protected function registerMacros()
    {
        Route::macro('module', function ($slug, $options = [], $resource_options = [], $resource = true) {

            $className = ucfirst(str_singular($slug));

            $customRoutes = $defaults = ['sort', 'publish', 'browser', 'bucket', 'media', 'feature', 'file'];

            if (isset($options['only'])) {
                $customRoutes = array_intersect($defaults, (array) $options['only']);
            } elseif (isset($options['except'])) {
                $customRoutes = array_diff($defaults, (array) $options['except']);
            }

            $groupPrefix = trim(str_replace('/', '.', Route::getLastGroupPrefix()), '.');
            $customRoutePrefix = !empty($groupPrefix) ? "{$groupPrefix}.{$slug}" : "{$slug}";

            foreach ($customRoutes as $route) {
                $routeSlug = "{$slug}/{$route}";
                $mapping = ['as' => $customRoutePrefix . ".{$route}", 'uses' => "{$className}Controller@{$route}"];

                if (in_array($route, ['browser', 'bucket', 'media', 'file'])) {
                    Route::get($routeSlug, $mapping);
                }

                if (in_array($route, ['publish', 'feature'])) {
                    Route::put($routeSlug, $mapping);
                }

                if (in_array($route, ['sort'])) {
                    Route::post($routeSlug, $mapping);
                }
            }

            if ($resource) {
                Route::resource($slug, "{$className}Controller", $resource_options);
            }
        });
    }
}
