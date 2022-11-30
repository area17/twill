<?php

namespace A17\Twill\Commands;

use A17\Twill\Commands\Traits\HandlesStubs;
use A17\Twill\Facades\TwillCapsules;
use A17\Twill\Helpers\Capsule;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleMake extends Command
{
    use HandlesStubs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:make:module {moduleName}
        {--B|hasBlocks}
        {--T|hasTranslation}
        {--S|hasSlug}
        {--M|hasMedias}
        {--F|hasFiles}
        {--P|hasPosition}
        {--R|hasRevisions}
        {--N|hasNesting}
        {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Twill Module';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var string[]
     */
    protected $modelTraits;

    /**
     * @var string[]
     */
    protected $repositoryTraits;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var bool
     */
    protected $blockable;

    /**
     * @var bool
     */
    protected $translatable;

    /**
     * @var bool
     */
    protected $sluggable;

    /**
     * @var bool
     */
    protected $mediable;

    /**
     * @var bool
     */
    protected $fileable;

    /**
     * @var bool
     */
    protected $sortable;

    /**
     * @var bool
     */
    protected $revisionable;

    /**
     * @var bool
     */
    protected $nestable;

    /**
     * @var bool
     */
    protected $defaultsAnswserToNo;

    /**
     * @var bool
     */
    protected $isCapsule = false;

    /**
     * @var bool
     */
    protected $isSingleton = false;

    /**
     * @var string
     */
    protected $moduleBasePath;

    /**
     * @var \A17\Twill\Helpers\Capsule
     */
    protected $capsule;

    /**
     * If true we save to flat directories.
     *
     * @var bool
     */
    protected $customDirs = false;

    /**
     * @param Filesystem $files
     * @param Composer $composer
     * @param Config $config
     */
    public function __construct(Filesystem $files, Composer $composer, Config $config)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
        $this->config = $config;

        $this->blockable = false;
        $this->translatable = false;
        $this->sluggable = false;
        $this->mediable = false;
        $this->fileable = false;
        $this->sortable = false;
        $this->revisionable = false;
        $this->nestable = false;

        $this->defaultsAnswserToNo = false;

        $this->modelTraits = [
            'HasBlocks',
            'HasTranslation',
            'HasSlug',
            'HasMedias',
            'HasFiles',
            'HasRevisions',
            'HasPosition',
            'HasNesting',
        ];
        $this->repositoryTraits = [
            'HandleBlocks',
            'HandleTranslations',
            'HandleSlugs',
            'HandleMedias',
            'HandleFiles',
            'HandleRevisions',
            '',
            'HandleNesting',
        ];
    }

    protected function checkCapsuleDirectory($dir)
    {
        if (file_exists($dir)) {
            if (! $this->option('force')) {
                $answer = $this->choice(
                    "Capsule path exists ($dir). Erase and overwrite?",
                    ['no', 'yes'],
                    $this->defaultsAnswserToNo
                        ? 0
                        : 1
                );
            }

            if ('yes' === ($answer ?? 'no') || $this->option('force')) {
                File::deleteDirectory($dir);

                if (file_exists($dir)) {
                    $this->info('Directory could not be deleted. Aborted.');
                    exit(1);
                }
            } else {
                $this->info('Aborted');

                exit(1);
            }
        }
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // e.g. newsItems
        $moduleName = Str::camel(Str::plural(lcfirst($this->argument('moduleName'))));

        // e.g. newsItem
        $singularModuleName = Str::camel(Str::singular(lcfirst($this->argument('moduleName'))));

        // e.g. NewsItems
        $moduleTitle = Str::studly($moduleName);

        // e.g. NewsItem
        $modelName = Str::studly(Str::singular($moduleName));

        if ($this->isCapsule && $this->option('packageDirectory') && $this->option('packageNamespace')) {
            $dir = base_path() . '/' . $this->option('packageDirectory') . '/src/Twill/Capsules/' . $moduleTitle;
            if (! $this->confirm('Creating capsule in ' . $dir, true)) {
                exit(1);
            }
            $this->customDirs = true;
            $this->capsule = new Capsule(
                $moduleTitle,
                $this->option('packageNamespace') . '\\Twill\\Capsules\\' . $moduleTitle,
                $dir
            );
        } elseif ($this->isCapsule) {
            $this->capsule = TwillCapsules::makeProjectCapsule($moduleTitle);
        }

        $enabledOptions = Collection::make($this->options())->only([
            'hasBlocks',
            'hasTranslation',
            'hasSlug',
            'hasMedias',
            'hasFiles',
            'hasPosition',
            'hasRevisions',
            'hasNesting',
        ])->filter(function ($enabled) {
            return $enabled;
        });

        if (count($enabledOptions) > 0) {
            $this->defaultsAnswserToNo = true;
        }

        $this->blockable = $this->checkOption('hasBlocks');
        $this->translatable = $this->checkOption('hasTranslation');
        $this->sluggable = $this->checkOption('hasSlug');
        $this->mediable = $this->checkOption('hasMedias');
        $this->fileable = $this->checkOption('hasFiles');
        $this->sortable = $this->checkOption('hasPosition');
        $this->revisionable = $this->checkOption('hasRevisions');
        $this->nestable = $this->checkOption('hasNesting');

        if ($this->nestable) {
            $this->sortable = true;
        }

        $activeTraits = [
            $this->blockable,
            $this->translatable,
            $this->sluggable,
            $this->mediable,
            $this->fileable,
            $this->revisionable,
            $this->sortable,
            $this->nestable,
        ];

        $this->createCapsuleNamespace($moduleTitle, $modelName);
        $this->createCapsulePath($moduleTitle, $modelName);

        $this->createMigration($moduleName);
        $this->createModels($modelName, $activeTraits);
        $this->createRepository($modelName, $activeTraits);
        $this->createController($moduleName, $modelName);
        $this->createRequest($modelName);
        $this->createViews($moduleName);

        if ($this->isCapsule) {
            if ($this->isSingleton) {
                $this->createCapsuleSingletonSeeder();
            } else {
                $this->createCapsuleSeed();
            }
            $this->createCapsuleRoutes();
        } elseif ($this->isSingleton) {
            $this->createSingletonSeed($modelName);
            $this->info("\nAdd to routes/admin.php:\n");
            $this->info("    Route::twillSingleton('{$singularModuleName}');\n");
        } else {
            $this->info("\nAdd to routes/admin.php:\n");
            $this->info("    Route::module('{$moduleName}');\n");
        }

        $navModuleName = $this->isSingleton ? $singularModuleName : $moduleName;
        $navTitle = $this->isSingleton ? $modelName : $moduleTitle;
        $navType = $this->isSingleton ? 'singleton' : 'module';

        if (! $this->customDirs) {
            $this->info("Setup a new CMS menu item in config/twill-navigation.php:\n");
            $this->info("    '{$navModuleName}' => [");
            $this->info("        'title' => '{$navTitle}',");
            $this->info("        '{$navType}' => true,");
            $this->info("    ],\n");

            if ($this->isCapsule) {
                $this->info("Setup your new Capsule in config/twill.php:\n");
                $this->info("    'capsules' => [");
                $this->info("        'list' => [");
                $this->info('            [');
                $this->info("                'name' => '{$this->capsule->name}',");
                $this->info("                'enabled' => true,");
                $this->info('            ],');
                $this->info('        ],');
                $this->info("    ],\n");
            }

            if ($this->isSingleton) {
                $this->info("Migrate your database & seed your singleton module:\n");
                $this->info("    php artisan migrate\n");
                $this->info("    php artisan db:seed {$modelName}Seeder\n");
            } else {
                $this->info("Migrate your database.\n");
            }
        }

        $this->info('Enjoy.');

        if ($this->nestable && ! class_exists('\Kalnoy\Nestedset\NestedSet')) {
            $this->warn("\nTo support module nesting, you must install the `kalnoy/nestedset` package:");
            $this->warn("\n    composer require kalnoy/nestedset\n");
        }

        $this->composer->dumpAutoloads();
    }

    /**
     * Creates a new module database migration file.
     *
     * @param string $moduleName
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function createMigration($moduleName = 'items')
    {
        $table = Str::snake($moduleName);
        $tableClassName = Str::studly($table);

        $migrationName = 'create_' . $table . '_tables';

        if (! count(glob($this->databasePath('migrations/*' . $migrationName . '.php')))) {
            $migrationPath = $this->databasePath() . '/migrations';

            $this->makeDir($migrationPath);

            $fullPath = $this->laravel['migration.creator']->create($migrationName, $migrationPath);

            $stub = str_replace(
                ['{{table}}', '{{singularTableName}}', '{{tableClassName}}'],
                [$table, Str::singular($table), $tableClassName],
                $this->files->get(__DIR__ . '/stubs/migration.stub')
            );

            if ($this->translatable) {
                $stub = preg_replace('/{{!hasTranslation}}[\s\S]+?{{\/!hasTranslation}}/', '', $stub);
            } else {
                $stub = str_replace([
                    '{{!hasTranslation}}',
                    '{{/!hasTranslation}}',
                ], '', $stub);
            }

            $stub = $this->renderStubForOption($stub, 'hasTranslation', $this->translatable);
            $stub = $this->renderStubForOption($stub, 'hasSlug', $this->sluggable);
            $stub = $this->renderStubForOption($stub, 'hasRevisions', $this->revisionable);
            $stub = $this->renderStubForOption($stub, 'hasPosition', $this->sortable);
            $stub = $this->renderStubForOption($stub, 'hasNesting', $this->nestable);

            $stub = preg_replace('/\}\);[\s\S]+?Schema::create/', "});\n\n        Schema::create", $stub);

            $this->files->put($fullPath, $stub);

            $this->info('Migration created successfully! Add some fields!');
        }
    }

    /**
     * Creates new model class files for the given model name and traits.
     *
     * @param string $modelName
     * @param array $activeTraits
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function createModels($modelName = 'Item', $activeTraits = [])
    {
        $modelClassName = $this->namespace('models', 'Models', $modelName);

        $modelsDir = $this->isCapsule ? $this->capsule->getModelsDir() : 'Models';

        $this->makeTwillDirectory($modelsDir);

        if ($this->translatable) {
            $this->makeTwillDirectory($baseDir = $this->isCapsule ? $this->capsule->getModelsDir() : "$modelsDir/Translations");

            $modelTranslationClassName = $modelName . 'Translation';

            $stub = str_replace(
                [
                    '{{modelTranslationClassName}}',
                    '{{modelClassWithNamespace}}',
                    '{{modelClassName}}',
                    '{{namespace}}',
                    '{{baseTranslationModel}}',
                ],
                [
                    $modelTranslationClassName,
                    $modelClassName,
                    $modelName,
                    $this->namespace('models', 'Models\Translations'),
                    config('twill.base_translation_model'),
                ],
                $this->files->get(__DIR__ . '/stubs/model_translation.stub')
            );

            $this->putTwillStub(twill_path("$baseDir/" . $modelTranslationClassName . '.php'), $stub);
        }

        if ($this->sluggable) {
            $this->makeTwillDirectory($baseDir = $this->isCapsule ? $this->capsule->getModelsDir() : "$modelsDir/Slugs");

            $modelSlugClassName = $modelName . 'Slug';

            $stub = str_replace(
                [
                    '{{modelSlugClassName}}',
                    '{{modelClassWithNamespace}}',
                    '{{modelName}}',
                    '{{namespace}}',
                    '{{baseSlugModel}}',
                ],
                [
                    $modelSlugClassName,
                    $modelClassName,
                    Str::snake($modelName),
                    $this->namespace('models', 'Models\Slugs'),
                    config('twill.base_slug_model'),
                ],
                $this->files->get(__DIR__ . '/stubs/model_slug.stub')
            );

            $this->putTwillStub(twill_path("$baseDir/" . $modelSlugClassName . '.php'), $stub);
        }

        if ($this->revisionable) {
            $this->makeTwillDirectory($baseDir = $this->isCapsule ? $this->capsule->getModelsDir() : "$modelsDir/Revisions");

            $modelRevisionClassName = $modelName . 'Revision';

            $stub = str_replace(
                [
                    '{{modelRevisionClassName}}',
                    '{{modelClassWithNamespace}}',
                    '{{modelName}}',
                    '{{namespace}}',
                    '{{baseRevisionModel}}',
                ],
                [
                    $modelRevisionClassName,
                    $modelClassName,
                    Str::snake($modelName),
                    $this->namespace('models', 'Models\Revisions'),
                    config('twill.base_revision_model'),
                ],
                $this->files->get(__DIR__ . '/stubs/model_revision.stub')
            );

            $this->putTwillStub(twill_path("$baseDir/" . $modelRevisionClassName . '.php'), $stub);
        }

        $activeModelTraits = [];

        foreach ($activeTraits as $index => $traitIsActive) {
            if ($traitIsActive) {
                ! isset($this->modelTraits[$index]) ?: $activeModelTraits[] = $this->modelTraits[$index];
            }
        }

        $activeModelTraitsString = empty($activeModelTraits) ? '' : 'use ' . rtrim(
            implode(', ', $activeModelTraits),
            ', '
        ) . ';';

        $activeModelTraitsImports = empty($activeModelTraits) ? '' : "use A17\Twill\Models\Behaviors\\" . implode(
            ";\nuse A17\Twill\Models\Behaviors\\",
            $activeModelTraits
        ) . ';';

        $activeModelImplements = $this->sortable ? 'implements Sortable' : '';

        if ($this->sortable) {
            $activeModelTraitsImports .= "\nuse A17\Twill\Models\Behaviors\Sortable;";
        }

        $stub = str_replace([
            '{{modelClassName}}',
            '{{modelTraits}}',
            '{{modelImports}}',
            '{{modelImplements}}',
            '{{namespace}}',
            '{{baseModel}}',
        ], [
            $modelName,
            $activeModelTraitsString,
            $activeModelTraitsImports,
            $activeModelImplements,
            $this->namespace('models', 'Models'),
            config('twill.base_model'),
        ], $this->files->get(__DIR__ . '/stubs/model.stub'));

        $stub = $this->renderStubForOption($stub, 'hasTranslation', $this->translatable);
        $stub = $this->renderStubForOption($stub, 'hasSlug', $this->sluggable);
        $stub = $this->renderStubForOption($stub, 'hasMedias', $this->mediable);
        $stub = $this->renderStubForOption($stub, 'hasPosition', $this->sortable);

        $this->putTwillStub(twill_path("$modelsDir/" . $modelName . '.php'), $stub);

        $this->info('Models created successfully! Fill your fillables!');
    }

    private function renderStubForOption($stub, $option, $enabled)
    {
        if ($enabled) {
            $stub = str_replace([
                '{{' . $option . '}}',
                '{{/' . $option . '}}',
            ], '', $stub);
        } else {
            $stub = preg_replace('/{{' . $option . '}}[\s\S]+?{{\/' . $option . '}}/', '', $stub);
        }

        return $stub;
    }

    /**
     * Creates new repository class file for the given model name.
     *
     * @param string $modelName
     * @param array $activeTraits
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function createRepository($modelName = 'Item', $activeTraits = [])
    {
        $modelsDir = $this->isCapsule ? $this->capsule->getRepositoriesDir() : 'Repositories';

        $modelClass = $this->isCapsule ? $this->capsule->getModel() : config(
            'twill.namespace'
        ) . "\Models\\{$modelName}";

        $this->makeTwillDirectory($modelsDir);

        $repositoryClassName = $modelName . 'Repository';

        $activeRepositoryTraits = [];

        foreach ($activeTraits as $index => $traitIsActive) {
            if ($traitIsActive) {
                ! isset($this->repositoryTraits[$index]) ?: $activeRepositoryTraits[] = $this->repositoryTraits[$index];
            }
        }

        $activeRepositoryTraits = array_filter($activeRepositoryTraits);

        $activeRepositoryTraitsString = empty($activeRepositoryTraits) ? '' : 'use ' . (empty($activeRepositoryTraits) ? '' : rtrim(
            implode(', ', $activeRepositoryTraits),
            ', '
        ) . ';');

        $activeRepositoryTraitsImports = empty($activeRepositoryTraits) ? '' : "use A17\Twill\Repositories\Behaviors\\" . implode(
            ";\nuse A17\Twill\Repositories\Behaviors\\",
            $activeRepositoryTraits
        ) . ';';

        $stub = str_replace(
            [
                '{{repositoryClassName}}',
                '{{modelName}}',
                '{{repositoryTraits}}',
                '{{repositoryImports}}',
                '{{namespace}}',
                '{{modelClass}}',
                '{{baseRepository}}',
            ],
            [
                $repositoryClassName,
                $modelName,
                $activeRepositoryTraitsString,
                $activeRepositoryTraitsImports,
                $this->namespace('repositories', 'Repositories'),
                $modelClass,
                config('twill.base_repository'),
            ],
            $this->files->get(__DIR__ . '/stubs/repository.stub')
        );

        $this->putTwillStub(twill_path("{$modelsDir}/" . $repositoryClassName . '.php'), $stub);

        $this->info('Repository created successfully! Control all the things!');
    }

    /**
     * Create a new controller class file for the given module name and model name.
     *
     * @param string $moduleName
     * @param string $modelName
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function createController($moduleName = 'items', $modelName = 'Item')
    {
        $controllerClassName = $modelName . 'Controller';

        $dir = $this->isCapsule ? $this->capsule->getControllersDir() : 'Http/Controllers/Admin';

        if ($this->isSingleton) {
            $baseController = config('twill.base_singleton_controller');
        } elseif ($this->nestable) {
            $baseController = config('twill.base_nested_controller');
        } else {
            $baseController = config('twill.base_controller');
        }

        $this->makeTwillDirectory($dir);

        $stub = str_replace(
            ['{{moduleName}}', '{{controllerClassName}}', '{{namespace}}', '{{baseController}}'],
            [
                $moduleName,
                $controllerClassName,
                $this->namespace('controllers', 'Http\Controllers\Admin'),
                $baseController,
            ],
            $this->files->get(__DIR__ . '/stubs/controller.stub')
        );

        $permalinkOption = '';
        $reorderOption = '';

        if (! $this->sluggable) {
            $permalinkOption = "'permalink' => false,";
        }

        if ($this->nestable) {
            $reorderOption = "'reorder' => true,";

            $stub = str_replace(['{{hasNesting}}', '{{/hasNesting}}'], '', $stub);
        } else {
            $stub = preg_replace('/{{hasNesting}}[\s\S]+?{{\/hasNesting}}/', '', $stub);
        }

        $stub = str_replace(
            ['{{permalinkOption}}', '{{reorderOption}}'],
            [$permalinkOption, $reorderOption],
            $stub
        );

        // Remove lines including only whitespace, leave true empty lines untouched
        $stub = preg_replace('/^[\s]+\n/m', '', $stub);

        $this->putTwillStub(twill_path("$dir/" . $controllerClassName . '.php'), $stub);

        $this->info('Controller created successfully! Define your index/browser/form endpoints options!');
    }

    /**
     * Creates a new request class file for the given model name.
     *
     * @param string $modelName
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function createRequest($modelName = 'Item')
    {
        $dir = $this->isCapsule ? $this->capsule->getRequestsDir() : 'Http/Requests/Admin';

        $this->makeTwillDirectory($dir);

        $requestClassName = $modelName . 'Request';

        $stub = str_replace(
            ['{{requestClassName}}', '{{namespace}}', '{{baseRequest}}'],
            [$requestClassName, $this->namespace('requests', 'Http\Requests\Admin'), config('twill.base_request')],
            $this->files->get(__DIR__ . '/stubs/request.stub')
        );

        $this->putTwillStub(twill_path("{$dir}/" . $requestClassName . '.php'), $stub);

        $this->info('Form request created successfully! Add some validation rules!');
    }

    /**
     * Creates appropriate module Blade view files.
     *
     * @param string $moduleName
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function createViews($moduleName = 'items')
    {
        $viewsPath = $this->viewPath($moduleName);

        $this->makeTwillDirectory($viewsPath);

        $formView = $this->translatable ? 'form_translatable' : 'form';

        $this->putTwillStub(
            $viewsPath . '/form.blade.php',
            $this->files->get(__DIR__ . '/stubs/' . $formView . '.blade.stub')
        );

        $this->info('Form view created successfully! Include your form fields using @formField directives!');
    }

    /**
     * Creates a basic routes file for the Capsule.
     *
     * @param string $moduleName
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function createCapsuleRoutes(): void
    {
        $this->makeDir($this->capsule->getRoutesFile());

        $stubFile = $this->isSingleton ? 'routes_singleton_admin.stub' : 'routes_admin.stub';

        $contents = str_replace(
            '{{moduleName}}',
            $this->isSingleton ? lcfirst($this->capsule->getSingular()) : $this->capsule->getModule(),
            $this->files->get(__DIR__ . '/stubs/' . $stubFile)
        );

        $this->putTwillStub($this->capsule->getRoutesFile(), $contents);

        $this->info('Routes file created successfully!');
    }

    /**
     * Creates a new capsule database seed file.
     *
     * @param string $moduleName
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function createCapsuleSeed(): void
    {
        $this->makeDir($this->capsule->getSeedsPsr4Path());

        $stub = $this->files->get(__DIR__ . '/stubs/database_seeder_capsule.stub');

        $stub = str_replace('{namespace}', $this->capsule->getSeedsNamespace(), $stub);

        $this->files->put("{$this->capsule->getSeedsPsr4Path()}/DatabaseSeeder.php", $stub);

        $this->info('Seed created successfully!');
    }

    /**
     * Creates a new singleton module database seed file.
     *
     * @param string $modelName
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function createSingletonSeed($modelName = 'Item')
    {
        $repositoryName = $modelName . 'Repository';
        $seederName = $modelName . 'Seeder';

        $dir = $this->databasePath('seeders');

        $this->makeTwillDirectory($dir);

        $stub = $this->files->get(__DIR__ . '/stubs/database_seeder_singleton.stub');

        $stub = $this->replaceVariables([
            'seederNamespace' => 'Database\\Seeders',
            'seederClassName' => $seederName,
            'modelClass' => "App\\Models\\$modelName",
            'modelClassName' => $modelName,
            'repositoryClass' => "App\\Repositories\\$repositoryName",
            'repositoryClassName' => $repositoryName,
        ], $stub);

        $stub = $this->replaceConditionals([
            'hasTranslations' => $this->translatable,
            '!hasTranslations' => ! $this->translatable,
        ], $stub);

        $stub = $this->removeEmptyLinesWithOnlySpaces($stub);

        $this->files->put("{$dir}/{$seederName}.php", $stub);

        $this->info('Seed created successfully!');
    }

    private function createCapsuleSingletonSeeder(): void
    {
        $modelName = $this->capsule->getSingular();
        $repositoryName = $this->capsule->getSingular() . 'Repository';
        $seederName = $this->capsule->getSingular() . 'Seeder';

        $dir = $this->capsule->getSeedsPsr4Path();

        $this->makeTwillDirectory($dir);

        $stub = $this->files->get(__DIR__ . '/stubs/database_seeder_singleton.stub');

        $stub = $this->replaceVariables([
            'seederNamespace' => $this->capsule->getDatabaseNamespace() . '\\Seeds',
            'seederClassName' => $seederName,
            'modelClass' => $this->capsule->getModelNamespace() . "\\$modelName",
            'modelClassName' => $modelName,
            'repositoryClass' => $this->capsule->getRepositoryClass(),
            'repositoryClassName' => $repositoryName,
        ], $stub);

        $stub = $this->replaceConditionals([
            'hasTranslations' => $this->translatable,
            '!hasTranslations' => ! $this->translatable,
        ], $stub);

        $stub = $this->removeEmptyLinesWithOnlySpaces($stub);

        $this->files->put("{$dir}/{$seederName}.php", $stub);

        $this->info('Seed created successfully!');
    }

    private function checkOption($option)
    {
        if (! $this->hasOption($option)) {
            return false;
        }

        if ($this->option($option) || $this->option('all')) {
            return true;
        }

        $questions = [
            'hasBlocks' => 'Do you need to use the block editor on this module?',
            'hasTranslation' => 'Do you need to translate content on this module?',
            'hasSlug' => 'Do you need to generate slugs on this module?',
            'hasMedias' => 'Do you need to attach images on this module?',
            'hasFiles' => 'Do you need to attach files on this module?',
            'hasPosition' => 'Do you need to manage the position of records on this module?',
            'hasRevisions' => 'Do you need to enable revisions on this module?',
            'hasNesting' => 'Do you need to enable nesting on this module?',
        ];

        $defaultAnswers = [
            'hasNesting' => 0,
        ];

        $currentDefaultAnswer = $this->defaultsAnswserToNo ? 0 : ($defaultAnswers[$option] ?? 1);

        return 'yes' === $this->choice($questions[$option], ['no', 'yes'], $currentDefaultAnswer);
    }

    public function createCapsulePath($moduleName, $modelName)
    {
        if (! $this->isCapsule) {
            $this->moduleBasePath = base_path();

            return;
        }

        $this->checkCapsuleDirectory(
            $this->moduleBasePath = $this->capsule->getPsr4Path()
        );

        $this->makeDir($this->moduleBasePath);
    }

    public function createCapsuleNamespace($module, $model)
    {
        $base = config('twill.capsules.namespace');

        $this->capsuleNamespace = "{$base}\\{$module}";
    }

    public function databasePath($path = '')
    {
        if (! $this->isCapsule) {
            return database_path($path);
        }

        return $this->capsule->getDatabasePsr4Path() . ($path ? '/' . $path : '');
    }

    public function makeDir($dir)
    {
        $info = pathinfo($dir);

        $dir = isset($info['extension']) ? $info['dirname'] : $dir;

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (! is_dir($dir)) {
            $this->info("It wasn't possible to create capsule directory $dir");

            die;
        }
    }

    public function makeTwillDirectory($path)
    {
        if ($this->customDirs) {
            $this->makeDir($path);
        } else {
            make_twill_directory($path);
        }
    }

    public function putTwillStub(string $path, string $stub): void
    {
        if ($this->customDirs) {
            $stub = str_replace(
                'namespace App\\',
                sprintf('namespace %s\\', config('twill.namespace')),
                $stub
            );

            file_put_contents($path, $stub);
        } else {
            twill_put_stub($path, $stub);
        }
    }

    public function namespace($type, $suffix, $class = null)
    {
        $class = (filled($class) ? "\\$class" : '');

        if (! $this->isCapsule) {
            return "App\\{$suffix}{$class}";
        }

        if ($type === 'models') {
            return $this->capsule->getModelNamespace() . $class;
        }

        if ($type === 'repositories') {
            return $this->capsule->getRepositoriesNamespace() . $class;
        }

        if ($type === 'controllers') {
            return $this->capsule->getControllersNamespace() . $class;
        }

        if ($type === 'requests') {
            return $this->capsule->getRequestsNamespace() . $class;
        }

        throw new \Exception('Missing Implementation.');
    }

    public function viewPath($moduleName)
    {
        if (! $this->isCapsule) {
            return $this->config->get('view.paths')[0] . '/admin/' . $moduleName;
        }

        $dir = "$this->moduleBasePath/resources/views/admin";
        $this->makeDir($dir);

        return $dir;
    }
}
