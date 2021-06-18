<?php

namespace A17\Twill;

use A17\Twill\Services\Capsules\HasCapsules;
use A17\Twill\Services\Capsules\Manager;
use A17\Twill\Services\Routing\HasRoutes;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

class CapsulesServiceProvider extends RouteServiceProvider
{
    use HasRoutes, HasCapsules;

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

    protected function registerCapsule($capsule)
    {
        $this->loadMigrationsFrom($capsule['migrations_dir']);
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

    public function testCanMakeCapsule()
    {
        $this->assertExitCodeIsGood(
            $this->artisan('twill:make:capsule', [
                'moduleName' => 'Cars',
                '--hasBlocks' => true,
                '--hasTranslation' => true,
                '--hasSlug' => true,
                '--hasMedias' => true,
                '--hasFiles' => true,
                '--hasPosition' => true,
                '--hasRevisions' => true,
            ])->run()
        );

        $this->assertFileExists(
            twill_path('Twill/Capsules/Cars/app/Models/Car.php')
        );

        $this->assertIsObject(
            $this->app->make('App\Twill\Capsules\Cars\Models\Car')
        );
    }
}
