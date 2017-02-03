<?php

namespace A17\CmsToolkit;

use A17\CmsToolkit\Http\Middleware\Impersonate;
use A17\CmsToolkit\Http\Middleware\NoDebugBar;
use A17\CmsToolkit\Http\Middleware\RedirectIfAuthenticated;
use A17\CmsToolkit\Http\Middleware\ValidateBackHistory;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
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
                $router->group(['middleware' => ['auth', 'impersonate', 'validateBackHistory']], function ($router) {
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
        /*
         * See Laravel 5.4 Changelog https://laravel.com/docs/5.4/upgrade
         * The middleware method of the Illuminate\Routing\Router class has been renamed to aliasMiddleware().
         */
        $middlewareRegisterMethod = method_exists(app('router'), 'aliasMiddleware') ? 'aliasMiddleware' : 'middleware';
        Route::$middlewareRegisterMethod('noDebugBar', NoDebugBar::class);
        Route::$middlewareRegisterMethod('impersonate', Impersonate::class);
        Route::$middlewareRegisterMethod('guest', RedirectIfAuthenticated::class);
        Route::$middlewareRegisterMethod('validateBackHistory', ValidateBackHistory::class);
    }

    protected function registerMacros()
    {
        Route::macro('module', function ($slug, $options = [], $resource_options = [], $resource = true) {

            $slugs = explode('.', $slug);
            $prefixSlug = str_replace('.', "/", $slug);
            $_slug = Arr::last($slugs);
            $className = implode("", array_map(function ($s) {
                return ucfirst(str_singular($s));
            }, $slugs));

            $customRoutes = $defaults = ['sort', 'publish', 'browser', 'bucket', 'media', 'feature', 'file', 'insert'];

            if (isset($options['only'])) {
                $customRoutes = array_intersect($defaults, (array) $options['only']);
            } elseif (isset($options['except'])) {
                $customRoutes = array_diff($defaults, (array) $options['except']);
            }

            $groupPrefix = trim(str_replace('/', '.', Route::getLastGroupPrefix()), '.');
            $customRoutePrefix = !empty($groupPrefix) ? "{$groupPrefix}.{$slug}" : "{$slug}";

            foreach ($customRoutes as $route) {
                $routeSlug = "{$prefixSlug}/{$route}";
                $mapping = ['as' => $customRoutePrefix . ".{$route}", 'uses' => "{$className}Controller@{$route}"];

                if (in_array($route, ['browser', 'bucket', 'media', 'file', 'insert'])) {
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
                $customRoutePrefix = !empty($groupPrefix) ? "{$groupPrefix}." : "";
                Route::group(['as' => $customRoutePrefix], function () use ($slug, $className, $resource_options) {
                    Route::resource($slug, "{$className}Controller", $resource_options);
                });
            }
        });
    }
}
