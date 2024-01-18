<?php

namespace A17\Twill;

use A17\Twill\Commands\BlockMake;
use A17\Twill\Commands\Build;
use A17\Twill\Commands\CapsuleInstall;
use A17\Twill\Commands\CreateExampleCommand;
use A17\Twill\Commands\CreateSuperAdmin;
use A17\Twill\Commands\Dev;
use A17\Twill\Commands\GenerateBlockComponent;
use A17\Twill\Commands\GenerateBlocks;
use A17\Twill\Commands\GenerateDocsCommand;
use A17\Twill\Commands\ServeDocsCommand;
use A17\Twill\Commands\TwillFlushManifest;
use A17\Twill\Commands\GeneratePackageCommand;
use A17\Twill\Commands\Install;
use A17\Twill\Commands\ListBlocks;
use A17\Twill\Commands\ListIcons;
use A17\Twill\Commands\MakeCapsule;
use A17\Twill\Commands\MakeSingleton;
use A17\Twill\Commands\ModuleMake;
use A17\Twill\Commands\RefreshCrops;
use A17\Twill\Commands\RefreshLQIP;
use A17\Twill\Commands\Release;
use A17\Twill\Commands\SetupDevTools;
use A17\Twill\Commands\SyncLang;
use A17\Twill\Commands\Update;
use A17\Twill\Commands\UpdateExampleCommand;
use A17\Twill\Http\ViewComposers\CurrentUser;
use A17\Twill\Http\ViewComposers\FilesUploaderConfig;
use A17\Twill\Http\ViewComposers\Localization;
use A17\Twill\Http\ViewComposers\MediasUploaderConfig;
use A17\Twill\Models\Block;
use A17\Twill\Models\File;
use A17\Twill\Models\Group;
use A17\Twill\Models\Media;
use A17\Twill\Models\User;
use A17\Twill\Services\FileLibrary\FileService;
use A17\Twill\Services\MediaLibrary\ImageService;
use Astrotomic\Translatable\TranslatableServiceProvider;
use Cartalyst\Tags\TagsServiceProvider;
use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use PragmaRX\Google2FAQRCode\Google2FA as Google2FAQRCode;
use Spatie\Activitylog\ActivitylogServiceProvider;

class TwillServiceProvider extends ServiceProvider
{
    /**
     * The Twill version.
     *
     * @var string
     */
    public const VERSION = '3.0.2';

    /**
     * Service providers to be registered.
     *
     * @var string[]
     */
    protected $providers = [
        RouteServiceProvider::class,
        ValidationServiceProvider::class,
        TranslatableServiceProvider::class,
        TagsServiceProvider::class,
        ActivitylogServiceProvider::class,
        CapsulesServiceProvider::class,
    ];

    /**
     * Bootstraps the package services.
     */
    public function boot(): void
    {
        $this->requireHelpers();

        $this->publishConfigs();
        $this->publishMigrations();
        $this->publishAssets();

        $this->registerCommands();

        $this->registerAndPublishViews();
        $this->registerAndPublishTranslations();

        $this->extendBlade();
        $this->addViewComposers();

        $this->check2FA();

        Blade::componentNamespace('A17\\Twill\\View\\Components\\Partials', 'twill.partials');
        Blade::componentNamespace('A17\\Twill\\View\\Components\\Layout', 'twill.layout');
        Blade::componentNamespace('A17\\Twill\\View\\Components\\Fields', 'twill');
    }

    private function requireHelpers(): void
    {
        require_once __DIR__ . '/Helpers/routes_helpers.php';
        require_once __DIR__ . '/Helpers/modules_helpers.php';
        require_once __DIR__ . '/Helpers/i18n_helpers.php';
        require_once __DIR__ . '/Helpers/media_library_helpers.php';
        require_once __DIR__ . '/Helpers/frontend_helpers.php';
        require_once __DIR__ . '/Helpers/migrations_helpers.php';
        require_once __DIR__ . '/Helpers/helpers.php';
    }

    /**
     * Registers the package services.
     */
    public function register(): void
    {
        $this->mergeConfigs();

        $this->registerProviders();
        $this->registerAliases();
        $this->registerFacades();

        $this->app->bind(TwillCapsules::class);

        \A17\Twill\Facades\TwillBlocks::registerComponentBlocks(
            '\\App\\View\\Components\\Twill\\Blocks',
            base_path('app/View/Components/Twill/Blocks')
        );

        foreach (config('twill.block_editor.directories.source.blocks') as $value) {
            TwillBlocks::$blockDirectories[$value['path']] = [
                'source' => $value['source'],
                'renderNamespace' => null
            ];
        }

        foreach (config('twill.block_editor.directories.source.repeaters') as $value) {
            TwillBlocks::$repeatersDirectories[$value['path']] = [
                'source' => $value['source'],
                'renderNamespace' => null
            ];
        }

        Relation::morphMap([
            'users' => User::class,
            'media' => Media::class,
            'files' => File::class,
            'blocks' => Block::class,
            'groups' => Group::class,
        ]);

        config(['twill.version' => $this->version()]);
    }

    private function registerFacades(): void
    {
        $this->app->bind('twill_util', function () {
            return new TwillUtil();
        });
    }

    /**
     * Registers the package service providers.
     */
    private function registerProviders(): void
    {
        // select auth service provider implementation
        $this->providers[] = config('twill.custom_auth_service_provider') ?: (
        config('twill.enabled.permissions-management') ?
            PermissionAuthServiceProvider::class : AuthServiceProvider::class
        );

        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }

        if (app()->environment('testing')) {
            $this->app->register(DuskServiceProvider::class);
        }

        if (config('twill.enabled.media-library')) {
            $this->app->singleton('imageService', function () {
                return $this->app->make(config('twill.media_library.image_service'));
            });
        }

        if (config('twill.enabled.file-library')) {
            $this->app->singleton('fileService', function () {
                return $this->app->make(config('twill.file_library.file_service'));
            });
        }
    }

    /**
     * Registers the package facade aliases.
     */
    private function registerAliases(): void
    {
        $loader = AliasLoader::getInstance();

        if (config('twill.enabled.media-library')) {
            $loader->alias('ImageService', ImageService::class);
        }

        if (config('twill.enabled.file-library')) {
            $loader->alias('FileService', FileService::class);
        }
    }

    /**
     * Defines the package configuration files for publishing.
     */
    private function publishConfigs(): void
    {
        if (config('twill.enabled.users-management')) {
            config([
                'auth.providers.twill_users' => [
                    'driver' => 'eloquent',
                    'model' => twillModel('user'),
                ],
            ]);

            config([
                'auth.guards.twill_users' => [
                    'driver' => 'session',
                    'provider' => 'twill_users',
                ],
            ]);

            if (blank(config('auth.passwords.twill_users'))) {
                config([
                    'auth.passwords.twill_users' => [
                        'provider' => 'twill_users',
                        'table' => config('twill.password_resets_table', 'twill_password_resets'),
                        'expire' => 60,
                        'throttle' => 60,
                    ],
                ]);
            }
        }

        config(
            ['activitylog.enabled' => config('twill.enabled.dashboard') ? true : config('twill.enabled.activitylog')]
        );
        config(['activitylog.subject_returns_soft_deleted_models' => true]);

        config(
            [
                'analytics.service_account_credentials_json' => config(
                    'twill.dashboard.analytics.service_account_credentials_json',
                    storage_path('app/analytics/service-account-credentials.json')
                ),
            ]
        );

        $this->publishes([__DIR__ . '/../config/twill-publish.php' => config_path('twill.php')], 'config');
        $this->publishes([__DIR__ . '/../config/translatable.php' => config_path('translatable.php')], 'config');
    }

    /**
     * Merges the package configuration files into the given configuration namespaces.
     */
    private function mergeConfigs(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/twill.php', 'twill');
        $this->mergeConfigFrom(__DIR__ . '/../config/frontend.php', 'twill.frontend');
        $this->mergeConfigFrom(__DIR__ . '/../config/seo.php', 'twill.seo');
        $this->mergeConfigFrom(__DIR__ . '/../config/block_editor.php', 'twill.block_editor');
        $this->mergeConfigFrom(__DIR__ . '/../config/enabled.php', 'twill.enabled');
        $this->mergeConfigFrom(__DIR__ . '/../config/file_library.php', 'twill.file_library');
        $this->mergeConfigFrom(__DIR__ . '/../config/media_library.php', 'twill.media_library');
        $this->mergeConfigFrom(__DIR__ . '/../config/imgix.php', 'twill.imgix');
        $this->mergeConfigFrom(__DIR__ . '/../config/glide.php', 'twill.glide');
        $this->mergeConfigFrom(__DIR__ . '/../config/twicpics.php', 'twill.twicpics');
        $this->mergeConfigFrom(__DIR__ . '/../config/dashboard.php', 'twill.dashboard');
        $this->mergeConfigFrom(__DIR__ . '/../config/models.php', 'twill.models');
        $this->mergeConfigFrom(__DIR__ . '/../config/oauth.php', 'twill.oauth');
        $this->mergeConfigFrom(__DIR__ . '/../config/disks.php', 'filesystems.disks');
        $this->mergeConfigFrom(__DIR__ . '/../config/autologin.php', 'twill.autologin');

        if (config('twill.enabled.permissions-management')) {
            $this->mergeConfigFrom(__DIR__ . '/../config/permissions.php', 'twill.permissions');
        }

        if (
            config('twill.media_library.endpoint_type') === 'local'
            && config('twill.media_library.disk') === 'twill_media_library'
        ) {
            $this->setLocalDiskUrl('media');
        }

        if (
            config('twill.file_library.endpoint_type') === 'local'
            && config('twill.file_library.disk') === 'twill_file_library'
        ) {
            $this->setLocalDiskUrl('file');
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/services.php', 'services');
    }

    private function setLocalDiskUrl($type): void
    {
        config([
            'filesystems.disks.twill_' . $type . '_library.url' => request()->getScheme()
                . '://'
                . str_replace(['http://', 'https://'], '', config('app.url'))
                . '/storage/'
                . trim(config('twill.' . $type . '_library.local_path'), '/ '),
        ]);
    }

    private function publishMigrations(): void
    {
        if (config('twill.load_default_migrations', true)) {
            $this->loadMigrationsFrom(__DIR__ . '/../migrations/default');
        }

        $this->publishes([
            __DIR__ . '/../migrations/default' => database_path('migrations'),
        ], 'migrations');

        $this->publishOptionalMigration('users-2fa');
        $this->publishOptionalMigration('users-oauth');
        $this->publishOptionalMigration('permissions-management');
    }

    private function publishOptionalMigration($feature): void
    {
        if (config('twill.enabled.' . $feature, false)) {
            if (config('twill.load_default_migrations', true)) {
                $this->loadMigrationsFrom(__DIR__ . '/../migrations/optional/' . $feature);
            }

            $this->publishes([
                __DIR__ . '/../migrations/optional/' . $feature => database_path('migrations'),
            ], 'migrations');
        }
    }

    private function publishAssets(): void
    {
        $this->publishes([
            __DIR__ . '/../twill-assets' => public_path(),
        ], 'assets');
    }

    private function registerAndPublishViews(): void
    {
        $viewPath = __DIR__ . '/../views';

        $this->loadViewsFrom($viewPath, 'twill');
        $this->publishes([$viewPath => resource_path('views/vendor/twill')], 'views');
    }

    private function registerCommands(): void
    {
        $commands = [
            Install::class,
            ModuleMake::class,
            MakeCapsule::class,
            MakeSingleton::class,
            BlockMake::class,
            ListIcons::class,
            ListBlocks::class,
            CreateSuperAdmin::class,
            RefreshLQIP::class,
            RefreshCrops::class,
            GenerateBlocks::class,
            Build::class,
            Update::class,
            Dev::class,
            SyncLang::class,
            CapsuleInstall::class,
            UpdateExampleCommand::class,
            CreateExampleCommand::class,
            SetupDevTools::class,
            GeneratePackageCommand::class,
            TwillFlushManifest::class,
            GenerateBlockComponent::class,
        ];

        if (app()->runningInConsole()) {
            $commands[] = Release::class;
            $commands[] = GenerateDocsCommand::class;
            $commands[] = ServeDocsCommand::class;
        }

        $this->commands($commands);
    }

    /**
     * Resolve and include a given view expression in the project, Twill internals or a package.
     *
     * @param string $view
     * @param string $expression
     */
    private function includeView($view, $expression): string
    {
        [$name] = str_getcsv($expression, ',', "'");

        if (preg_match('#::#', $name)) {
            // if there's a namespace separator, we'll assume it's a package
            [$namespace, $name] = preg_split('#::#', $name);
            $partialNamespace = "$namespace::admin.";
        } else {
            $partialNamespace = view()->exists('twill.' . $view . $name) ? 'twill.' : 'twill::';
        }

        $view = $partialNamespace . $view . $name;

        $expression = explode(',', $expression);
        array_shift($expression);

        if (class_exists(Blade::getClassComponentNamespaces()['twill'] . '\\' . Str::studly($name))) {
            $expression = implode(',', $expression);
            if ($expression === '') {
                $expression = '[]';
            }

            $expression = str_replace("'", "\\'", $expression);

            // Fix dash variables that we know.
            $expression = str_replace('toolbar-options', 'toolbarOptions', $expression);

            $php = '<?php' . PHP_EOL;
            $php .= "\$data = eval('return $expression;');";
            $php .= '$fieldAttributes = "";';
            $php .= 'foreach(array_keys($data) as $attribute) {';
            $php .= '  $fieldAttributes .= " :$attribute=\'$" . $attribute . "\'";';
            $php .= '}' . PHP_EOL;
            $php .= 'if ($renderForBlocks ?? false) {';
            $php .= '  $fieldAttributes .= " :render-for-blocks=\'true\'";';
            $php .= '}';
            $php .= 'if ($renderForModal ?? false) {';
            $php .= '  $fieldAttributes .= " :render-for-modal=\'true\'";';
            $php .= '}';
            $php .= '$name = "' . $name . '";';
            $php .= 'echo Blade::render("<x-twill::$name $fieldAttributes />", $data); ?>';

            return $php;
        }

        // Legacy behaviour.
        // @TODO: Not sure if we should keep this.
        $expression = '(' . implode(',', $expression) . ')';
        if ($expression === '()') {
            $expression = '([])';
        }

        return "<?php echo \$__env->make('{$view}', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->with{$expression}->render(); ?>";
    }

    /**
     * Defines the package additional Blade Directives.
     */
    private function extendBlade(): void
    {
        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $this->registerNullBladeDirectives($blade);

        $blade->directive('dumpData', function ($data) {
            return sprintf(
                "<?php (new Symfony\Component\VarDumper\VarDumper)->dump(%s); exit; ?>",
                null != $data ? $data : 'get_defined_vars()'
            );
        });

        $blade->directive('formField', function ($expression) {
            return $this->includeView('partials.form._', $expression);
        });

        $blade->directive('partialView', function ($expression) {
            $expressionAsArray = str_getcsv($expression, ',', "'");

            [$moduleName, $viewName] = $expressionAsArray;
            $partialNamespace = 'twill::partials';

            $viewModule = "twillViewName($moduleName, '{$viewName}')";
            $viewApplication = "'twill.partials.{$viewName}'";
            $viewModuleTwill = "'twill::'.$moduleName.'.{$viewName}'";
            $view = $partialNamespace . '.' . $viewName;

            if (!isset($moduleName) || is_null($moduleName)) {
                $viewModule = $viewApplication;
            }

            $expression = explode(',', $expression);
            $expression = array_slice($expression, 2);
            $expression = '(' . implode(',', $expression) . ')';
            if ($expression === '()') {
                $expression = '([])';
            }

            return "<?php
            if( view()->exists($viewModule)) {
                echo \$__env->make($viewModule, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->with{$expression}->render();
            } elseif( view()->exists($viewApplication)) {
                echo \$__env->make($viewApplication, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->with{$expression}->render();
            } elseif( view()->exists($viewModuleTwill)) {
                echo \$__env->make($viewModuleTwill, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->with{$expression}->render();
            } elseif( view()->exists('$view')) {
                echo \$__env->make('$view', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->with{$expression}->render();
            }
            ?>";
        });

        $blade->directive('pushonce', function ($expression) {
            [$pushName, $pushSub] = explode(':', trim(substr($expression, 1, -1)));
            $key = '__pushonce_' . $pushName . '_' . str_replace('-', '_', $pushSub);

            return "<?php if(! isset(\$__env->{$key})): \$__env->{$key} = 1; \$__env->startPush('{$pushName}'); ?>";
        });

        $blade->directive('endpushonce', function () {
            return '<?php $__env->stopPush(); endif; ?>';
        });

        $blade->component('twill::partials.form.utils._fieldset', 'formFieldset');
        $blade->component('twill::partials.form.utils._columns', 'formColumns');
        $blade->component('twill::partials.form.utils._collapsed_fields', 'formCollapsedFields');
        $blade->component('twill::partials.form.utils._connected_fields', 'formConnectedFields');
        $blade->component('twill::partials.form.utils._inline_checkboxes', 'formInlineCheckboxes');

        $blade->component('twill::partials.form.utils._fieldset', 'twill::formFieldset');
        $blade->component('twill::partials.form.utils._columns', 'twill::formColumns');
        $blade->component('twill::partials.form.utils._collapsed_fields', 'twill::formCollapsedFields');
        $blade->component('twill::partials.form.utils._connected_fields', 'twill::formConnectedFields');
        $blade->component('twill::partials.form.utils._inline_checkboxes', 'twill::formInlineCheckboxes');

        $blade->component('twill::partials.form.utils._field_rows', 'twill::fieldRows');

        if (method_exists($blade, 'aliasComponent')) {
            $blade->aliasComponent('twill::partials.form.utils._fieldset', 'formFieldset');
            $blade->aliasComponent('twill::partials.form.utils._columns', 'formColumns');
            $blade->aliasComponent('twill::partials.form.utils._collapsed_fields', 'formCollapsedFields');
            $blade->aliasComponent('twill::partials.form.utils._connected_fields', 'formConnectedFields');
            $blade->aliasComponent('twill::partials.form.utils._inline_checkboxes', 'formInlineCheckboxes');
        }
    }

    /**
     * Null blade directives are used for cleaning up the form, block and repeater blade files.
     */
    private function registerNullBladeDirectives($blade): void
    {
        $nullCallBack = function () {
            return null;
        };

        $keys = ['Block', 'Repeater', 'Prop'];
        $props = [
            'Title',
            'TitleField',
            'Icon',
            'Group',
            'Trigger',
            'Max',
            'Compiled',
            'Component',
            'ValidationRules',
            'ValidationRulesForTranslatedFields',
            'SelectTrigger',
        ];

        foreach ($keys as $key) {
            foreach ($props as $prop) {
                $blade->directive("twill{$key}{$prop}", $nullCallBack);
            }
        }
    }

    /**
     * Registers the package additional View Composers.
     */
    private function addViewComposers(): void
    {
        View::composer(['twill.*', 'twill::*'], CurrentUser::class);

        if (config('twill.enabled.media-library')) {
            View::composer('twill::layouts.main', MediasUploaderConfig::class);
        }

        if (config('twill.enabled.file-library')) {
            View::composer('twill::layouts.main', FilesUploaderConfig::class);
        }

        View::composer(['twill.*', 'templates.*', 'twill::*'], function ($view) {
            $with = array_merge([
                'renderForBlocks' => false,
                'renderForModal' => false,
            ], $view->getData());

            return $view->with($with);
        });

        View::composer(['twill.*', 'twill::*'], Localization::class);
    }

    /**
     * Registers and publishes the package additional translations.
     */
    private function registerAndPublishTranslations(): void
    {
        $translationPath = __DIR__ . '/../lang';

        $this->loadTranslationsFrom($translationPath, 'twill');
        $this->publishes([$translationPath => resource_path('lang/vendor/twill')], 'translations');
    }

    /**
     * Get the version number of Twill.
     */
    public function version(): string
    {
        return static::VERSION;
    }

    /**
     * In case 2FA is enabled, we need to check if a QRCode compatible package is
     * installed.
     */
    public function check2FA(): void
    {
        if (!$this->app->runningInConsole() || !config('twill.enabled.users-2fa')) {
            return;
        }

        if (blank((new Google2FAQRCode())->getQrCodeService())) {
            throw new Exception(
                'Twill ERROR: As you have 2FA enabled, you also need to install a QRCode service package, please check https://github.com/antonioribeiro/google2fa-qrcode#built-in-qrcode-rendering-services'
            );
        }
    }
}
