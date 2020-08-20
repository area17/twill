<?php

namespace A17\Twill;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use A17\Twill\Services\Capsules\Manager;
use A17\Twill\Services\Routing\HasRoutes;
use A17\Twill\Services\Capsules\HasCapsules;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

class CapsulesServiceProvider extends RouteServiceProvider
{
    use HasRoutes, HasCapsules;

    protected function mergeTwillConfig()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/capsules.php',
            'twill.capsules'
        );

        $this->app
            ->make('config')
            ->set('twill.capsules.list', $this->getCapsuleList());

        $this->app->make('config')->set('twill.capsules.loaded', true);
    }

    public function register()
    {
        $this->registerConfig();
    }

    protected function registerConfig()
    {
        $this->mergeTwillConfig();

        $this->registerCapsules();

        $this->registerViewPaths();

        $this->registerManager();
    }

    public function registerCapsules()
    {
        $this->getCapsuleList()->map(function ($capsule) {
            $this->registerCapsule($capsule);
        });
    }

    protected function registerCapsule($capsule)
    {
        $this->loadMigrationsFrom($capsule['migrations_dir']);
    }

    public function map(Router $router)
    {
        $this->getCapsuleList()->each(function ($capsule) use ($router) {
            $this->registerCapsuleRoutes($router, $capsule);
        });
    }

    public function registerCapsuleRoutes($router, $capsule)
    {
        $this->registerRoutes(
            $router,
            $this->getRouteGroupOptions(),
            $this->getRouteMiddleware(),
            $this->supportSubdomainRouting(),
            "{$capsule['namespace']}\Http\Controllers",
            $capsule['routes_file']
        );
    }

    public function registerViewPaths()
    {
        $this->callAfterResolving('view', function ($view) {
            $view->addLocation(config('twill.capsules.path'));
        });
    }

    public function registerManager()
    {
        $this->app->instance('twill.capsules.manager', new Manager());
    }
}
