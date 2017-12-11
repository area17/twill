<?php

namespace A17\CmsToolkit;

use A17\CmsToolkit\Commands\CreateSuperAdmin;
use A17\CmsToolkit\Commands\GenerateBlocks;
use A17\CmsToolkit\Commands\ModuleMake;
use A17\CmsToolkit\Commands\RefreshLQIP;
use A17\CmsToolkit\Commands\Setup;
use A17\CmsToolkit\Http\ViewComposers\ActiveNavigation;
use A17\CmsToolkit\Http\ViewComposers\CurrentUser;
use A17\CmsToolkit\Http\ViewComposers\UploaderConfig;
use A17\CmsToolkit\Models\File;
use A17\CmsToolkit\Models\Media;
use A17\CmsToolkit\Models\User;
use A17\CmsToolkit\Services\FileLibrary\Disk;
use A17\CmsToolkit\Services\FileLibrary\FileService;
use A17\CmsToolkit\Services\MediaLibrary\ImageService;
use Barryvdh\Debugbar\Facade as Debugbar;
use Barryvdh\Debugbar\ServiceProvider as DebugbarServiceProvider;
use Cartalyst\Tags\TagsServiceProvider;
use Dimsav\Translatable\TranslatableServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Lsrur\Inspector\Facade\Inspector;
use Lsrur\Inspector\InspectorServiceProvider;
use Sofa\ModelLocking\ServiceProvider as ModelLockingServiceProvider;
use View;

class CmsToolkitServiceProvider extends ServiceProvider
{
    protected $providers = [
        RouteServiceProvider::class,
        AuthServiceProvider::class,
        ValidationServiceProvider::class,
        TranslatableServiceProvider::class,
        TagsServiceProvider::class,
        ModelLockingServiceProvider::class,
    ];

    public function boot()
    {
        $this->requireHelpers();

        $this->publishConfigs();
        $this->publishMigrations();
        $this->publishPublicAssets();

        $this->registerCommands();

        $this->registerAndPublishViews();

        $this->extendBlade();
        $this->addViewComposers();
    }

    public function register()
    {
        $this->mergeConfigs();

        $this->registerProviders();
        $this->registerAliases();

        Relation::morphMap([
            'users' => User::class,
            'media' => Media::class,
            'files' => File::class,
        ]);
    }

    public function provides()
    {
        return ['Illuminate\Contracts\Debug\ExceptionHandler'];
    }

    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }

        if ($this->app->environment('development', 'local', 'staging')) {
            if (config('cms-toolkit.debug.use_inspector', false)) {
                $this->app->register(InspectorServiceProvider::class);
            } else {
                $this->app->register(DebugbarServiceProvider::class);
            }
        }

        if (config('cms-toolkit.enabled.media-library')) {
            $this->app->singleton('imageService', function () {
                return $this->app->make(config('cms-toolkit.media_library.image_service'));
            });
        }

        if (config('cms-toolkit.enabled.file-library')) {
            $this->app->singleton('fileService', function () {
                return $this->app->make(Disk::class);
            });
        }
    }

    private function registerAliases()
    {
        $loader = AliasLoader::getInstance();

        if (config('cms-toolkit.debug.use_inspector', false)) {
            $loader->alias('Inspector', Inspector::class);
        } else {
            $loader->alias('Debugbar', Debugbar::class);
        }

        if (config('cms-toolkit.enabled.media-library')) {
            $loader->alias('ImageService', ImageService::class);
        }

        if (config('cms-toolkit.enabled.media-library')) {
            $loader->alias('FileService', FileService::class);
        }

    }

    private function publishConfigs()
    {
        if (config('cms-toolkit.enabled.users-management')) {
            config(['auth.providers.users' => require __DIR__ . '/../config/auth.php']);
        }

        $this->publishes([__DIR__ . '/../config/cms-toolkit-publish.php' => config_path('cms-toolkit.php')], 'config');
        $this->publishes([__DIR__ . '/../config/cms-navigation.php' => config_path('cms-navigation.php')], 'config');
    }

    private function mergeConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/cms-toolkit.php', 'cms-toolkit');
        $this->mergeConfigFrom(__DIR__ . '/../config/disks.php', 'filesystems.disks');
        $this->mergeConfigFrom(__DIR__ . '/../config/frontend.php', 'cms-toolkit.frontend');
        $this->mergeConfigFrom(__DIR__ . '/../config/debug.php', 'cms-toolkit.debug');
        $this->mergeConfigFrom(__DIR__ . '/../config/seo.php', 'cms-toolkit.seo');
        $this->mergeConfigFrom(__DIR__ . '/../config/blocks.php', 'cms-toolkit.block_editor');
        $this->mergeConfigFrom(__DIR__ . '/../config/enabled.php', 'cms-toolkit.enabled');
        $this->mergeConfigFrom(__DIR__ . '/../config/imgix.php', 'cms-toolkit.imgix');
        $this->mergeConfigFrom(__DIR__ . '/../config/media-library.php', 'cms-toolkit.media_library');
        $this->mergeConfigFrom(__DIR__ . '/../config/file-library.php', 'cms-toolkit.file_library');
    }

    private function publishMigrations()
    {
        $migrations = ['CreateTagsTables', 'CreateModelLocksTable', 'CreateBlocksTable'];

        $optionalMigrations = [
            'CreateUsersTables' => 'users-management',
            'CreateFilesTables' => 'file-library',
            'CreateMediasTables' => 'media-library',
            'CreateFeaturesTable' => 'buckets',
        ];

        if ($this->app->runningInConsole()) {
            foreach ($migrations as $migration) {
                $this->publishMigration($migration);
            }

            foreach ($optionalMigrations as $migration => $feature) {
                if (config('cms-toolkit.enabled.' . $feature)) {
                    $this->publishMigration($migration);
                }
            }
        }
    }

    private function publishMigration($migration)
    {
        if (!class_exists($migration)) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__ . '/../migrations/' . snake_case($migration) . '.php' => database_path('migrations/' . $timestamp . '_' . snake_case($migration) . '.php'),
            ], 'migrations');
        }
    }

    private function registerAndPublishViews()
    {
        $viewPath = __DIR__ . '/../views';

        $this->loadViewsFrom($viewPath, 'cms-toolkit');
        $this->publishes([$viewPath => resource_path('views/vendor/cms-toolkit')], 'views');
    }

    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Setup::class,
                ModuleMake::class,
                CreateSuperAdmin::class,
                RefreshLQIP::class,
                GenerateBlocks::class,
            ]);
        }
    }

    private function requireHelpers()
    {
        require_once __DIR__ . '/Helpers/routes_helpers.php';
        require_once __DIR__ . '/Helpers/i18n_helpers.php';
        require_once __DIR__ . '/Helpers/media_library_helpers.php';
        require_once __DIR__ . '/Helpers/frontend_helpers.php';
        require_once __DIR__ . '/Helpers/migrations_helpers.php';
        require_once __DIR__ . '/Helpers/helpers.php';
    }

    private function publishPublicAssets()
    {
        $this->publishes([__DIR__ . '/../assets' => public_path('assets')], 'assets');
    }

    private function includeView($view, $expression)
    {
        list($name) = str_getcsv($expression, ',', '\'');

        $partialNamespace = view()->exists('admin.' . $view . $name) ? 'admin.' : 'cms-toolkit::';

        $view = $partialNamespace . $view . $name;

        $expression = explode(',', $expression);
        array_shift($expression);
        $expression = "(" . implode(',', $expression) . ")";
        if ($expression === "()") {
            $expression = '([])';
        }
        return "<?php echo \$__env->make('{$view}', array_except(get_defined_vars(), ['__data', '__path']))->with{$expression}->render(); ?>";
    }

    private function extendBlade()
    {
        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->directive('dd', function ($param) {
            return "<?php dd({$param}); ?>";
        });

        $blade->directive('dumpData', function ($data) {
            return sprintf("<?php (new Illuminate\Support\Debug\Dumper)->dump(%s); exit; ?>",
                null != $data ? $data : "get_defined_vars()");
        });

        $blade->directive('formField', function ($expression) use ($blade) {
            return $this->includeView('partials.form._', $expression);
        });

        $blade->directive('partialView', function ($expression) use ($blade) {

            $expressionAsArray = str_getcsv($expression, ',', '\'');

            list($moduleName, $viewName) = $expressionAsArray;
            $partialNamespace = 'cms-toolkit::partials';

            $viewModule = "'admin.'.$moduleName.'.{$viewName}'";
            $viewApplication = "'admin.partials.{$viewName}'";
            $viewModuleToolkit = "'cms-toolkit::'.$moduleName.'.{$viewName}'";
            $view = $partialNamespace . "." . $viewName;

            if (!isset($moduleName) || is_null($moduleName)) {
                $viewModule = $viewApplication;
            }

            $expression = explode(',', $expression);
            $expression = array_slice($expression, 2);
            $expression = "(" . implode(',', $expression) . ")";
            if ($expression === "()") {
                $expression = '([])';
            }

            return "<?php
            if( view()->exists($viewModule)) {
                echo \$__env->make($viewModule, array_except(get_defined_vars(), ['__data', '__path']))->with{$expression}->render();
            } elseif( view()->exists($viewApplication)) {
                echo \$__env->make($viewApplication, array_except(get_defined_vars(), ['__data', '__path']))->with{$expression}->render();
            } elseif( view()->exists($viewModuleToolkit)) {
                echo \$__env->make($viewModuleToolkit, array_except(get_defined_vars(), ['__data', '__path']))->with{$expression}->render();
            } elseif( view()->exists('$view')) {
                echo \$__env->make('$view', array_except(get_defined_vars(), ['__data', '__path']))->with{$expression}->render();
            }
            ?>";
        });
    }

    private function addViewComposers()
    {
        if (config('cms-toolkit.enabled.users-management')) {
            View::composer(['admin.*', 'cms-toolkit::*'], CurrentUser::class);
        }

        if (config('cms-toolkit.enabled.media-library')) {
            View::share('mediaLibraryUrl', route('admin.media-library.medias.index'));
            View::composer('cms-toolkit::layouts.main', UploaderConfig::class);
        }

        View::composer('cms-toolkit::partials.navigation.*', ActiveNavigation::class);

        View::composer(['admin.*', 'cms-toolkit::*'], function ($view) {
            return $view->with(['renderForBlocks' => false]);
        });

    }

    private function registerAndPublishTranslations()
    {
        $translationPath = __DIR__ . '/../lang';

        $this->loadTranslationsFrom($translationPath, 'cms-toolkit');
        $this->publishes([$translationPath => resource_path('lang/vendor/cms-toolkit')], 'translations');
    }
}
