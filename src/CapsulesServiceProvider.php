<?php

namespace A17\Twill;

use A17\Twill\Services\Capsules\HasCapsules;
use A17\Twill\Services\Capsules\Manager;
use A17\Twill\Services\Routing\HasRoutes;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

class CapsulesServiceProvider extends RouteServiceProvider
{
    use HasRoutes;
    use HasCapsules;

    public static $capsulesBootstrapped = false;

    protected $manager;

    protected function mergeTwillConfig()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/capsules.php',
            'twill.capsules'
        );

        $this->app
            ->make('config')
            ->set('twill.capsules.list', $this->getCapsuleList()->toArray());

        $this->app->make('config')->set('twill.capsules.loaded', true);
    }

    public function register()
    {
        $this->registerManager();
        $this->mergeTwillConfig();
        $this->bootCapsules();
    }

    public function boot()
    {
        $this->registerCapsules();
        $this->registerViewPaths();
    }

    public function registerCapsules()
    {
        $this->manager->getCapsuleList()->map(function ($capsule) {
            $this->registerCapsule($capsule);
        });
    }

    /*
     * Boot the capsules so their psr, config and service providers are booted.
     *
     * @see HasCapsules::bootstrapCapsule
     */
    public function bootCapsules()
    {
        if (! self::$capsulesBootstrapped) {
            $this->getCapsuleList()
                ->where('enabled', true)
                ->each(function ($capsule) {
                    $this->bootstrapCapsule($capsule);
                });
            self::$capsulesBootstrapped = true;
        }
    }

    protected function registerCapsule($capsule)
    {
        $this->loadMigrationsFrom($capsule['migrations_dir']);
        $this->loadTranslationsFrom($capsule['lang_dir'], 'twill:capsules:' . $capsule['module']);
    }

    public function registerViewPaths()
    {
        if (file_exists(config('twill.capsules.path'))) {
            $callback = function ($view) {
                $view->addLocation(config('twill.capsules.path'));
            };

            $this->app->afterResolving('view', $callback);

            if ($this->app->resolved('view')) {
                $callback($this->app->make('view'), $this->app);
            }
        }
    }

    public function registerManager()
    {
        $this->app->instance(
            'twill.capsules.manager',
            $this->manager = new Manager()
        );
    }
}
