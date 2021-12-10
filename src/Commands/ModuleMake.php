<?php

namespace A17\Twill\Commands;

use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleMake extends Command
{
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
    protected $defaultsAnswserToNo;

    /**
     * @var bool
     */
    protected $isCapsule = false;

    /**
     * @var string
     */
    protected $moduleBasePath;

    /**
     * @var string
     */
    protected $capsule;

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

        $this->defaultsAnswserToNo = false;

        $this->modelTraits = ['HasBlocks', 'HasTranslation', 'HasSlug', 'HasMedias', 'HasFiles', 'HasRevisions', 'HasPosition'];
        $this->repositoryTraits = ['HandleBlocks', 'HandleTranslations', 'HandleSlugs', 'HandleMedias', 'HandleFiles', 'HandleRevisions'];
    }

    protected function checkCapsuleDirectory($dir)
    {
        if (file_exists($dir)) {
            if (!$this->option('force')) {
                $answer = $this->choice("Capsule path exists ({$dir}). Erase and overwrite?",
                    ['no', 'yes'], $this->defaultsAnswserToNo
                    ? 0
                    : 1);
            }

            if ('yes' === ($answer ?? 'no') || $this->option('force')) {
                File::deleteDirectory($dir);

                if (file_exists($dir)) {
                    $this->info("Directory could not be deleted. Aborted.");
                    die;
                }
            } else {
                $this->info("Aborted");

                die;
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
        $moduleName = Str::camel(Str::plural(lcfirst($this->argument('moduleName'))));

        $this->capsule = app('twill.capsules.manager')->makeCapsule(['name' => $moduleName], config("twill.capsules.path"));

        $enabledOptions = Collection::make($this->options())->only([
            'hasBlocks',
            'hasTranslation',
            'hasSlug',
            'hasMedias',
            'hasFiles',
            'hasPosition',
            'hasRevisions',
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

        $activeTraits = [
            $this->blockable,
            $this->translatable,
            $this->sluggable,
            $this->mediable,
            $this->fileable,
            $this->revisionable,
            $this->sortable,
        ];

        $modelName = Str::studly(Str::singular($moduleName));

        $this->createCapsuleNamespace(Str::studly($moduleName), $modelName);

        $this->createCapsulePath(Str::studly($moduleName), $modelName);

        $this->createMigration($moduleName);
        $this->createModels($modelName, $activeTraits);
        $this->createRepository($modelName, $activeTraits);
        $this->createController($moduleName, $modelName);
        $this->createRequest($modelName);
        $this->createViews($moduleName);

        if ($this->isCapsule) {
            $this->createRoutes($moduleName);
            $this->createSeed($moduleName);
        } else {
            $this->info("Add Route::module('{$moduleName}'); to your admin routes file.");
        }

        $this->info("Setup a new CMS menu item in config/twill-navigation.php:");

        $navTitle = Str::studly($moduleName);

        $this->info("
            '{$moduleName}' => [
                'title' => '{$navTitle}',
                'module' => true
            ]
        ");

        if ($this->isCapsule) {
            $this->info("Setup your new Capsule on config/twill.php:");

            $navTitle = Str::studly($moduleName);

            $this->info("
                'capsules' => [
                    'list' => [
                        [
                            'name' => '{$this->capsule['name']}',
                            'enabled' => true
                        ]
                    ]
                ]
            ");
        }

        $this->info("Migrate your database.\n");

        $this->info("Enjoy.");

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

        $className = "Create{$tableClassName}Tables";

        $migrationName = 'create_' . $table . '_tables';

        if (!count(glob($this->databasePath('migrations/*' . $migrationName . '.php')))) {
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

            $stub = preg_replace('/\}\);[\s\S]+?Schema::create/', "});\n\n        Schema::create", $stub);

            $this->files->put($fullPath, $stub);

            $this->info("Migration created successfully! Add some fields!");
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

        $modelsDir = $this->isCapsule ? $this->capsule['models_dir'] : 'Models';

        $this->makeTwillDirectory($modelsDir);

        if ($this->translatable) {
            $this->makeTwillDirectory($baseDir = $this->isCapsule ? $modelsDir : "{$modelsDir}/Translations");

            $modelTranslationClassName = $modelName . 'Translation';

            $stub = str_replace(
                ['{{modelTranslationClassName}}', '{{modelClassWithNamespace}}', '{{modelClassName}}', '{{namespace}}', '{{baseTranslationModel}}'],
                [$modelTranslationClassName, $modelClassName, $modelName, $this->namespace('models', 'Models\Translations'), config('twill.base_translation_model')],
                $this->files->get(__DIR__ . '/stubs/model_translation.stub')
            );

            twill_put_stub(twill_path("{$baseDir}/" . $modelTranslationClassName . '.php'), $stub);
        }

        if ($this->sluggable) {
            $this->makeTwillDirectory($baseDir = $this->isCapsule ? $modelsDir : "{$modelsDir}/Slugs");

            $modelSlugClassName = $modelName . 'Slug';

            $stub = str_replace(
                ['{{modelSlugClassName}}', '{{modelClassWithNamespace}}', '{{modelName}}', '{{namespace}}', '{{baseSlugModel}}'],
                [$modelSlugClassName, $modelClassName, Str::snake($modelName), $this->namespace('models', 'Models\Slugs'), config('twill.base_slug_model')],
                $this->files->get(__DIR__ . '/stubs/model_slug.stub')
            );

            twill_put_stub(twill_path("{$baseDir}/" . $modelSlugClassName . '.php'), $stub);
        }

        if ($this->revisionable) {
            $this->makeTwillDirectory($baseDir = $this->isCapsule ? $modelsDir : "{$modelsDir}/Revisions");

            $modelRevisionClassName = $modelName . 'Revision';

            $stub = str_replace(
                ['{{modelRevisionClassName}}', '{{modelClassWithNamespace}}', '{{modelName}}', '{{namespace}}', '{{baseRevisionModel}}'],
                [$modelRevisionClassName, $modelClassName, Str::snake($modelName), $this->namespace('models', 'Models\Revisions'), config('twill.base_revision_model')],
                $this->files->get(__DIR__ . '/stubs/model_revision.stub')
            );

            twill_put_stub(twill_path("{$baseDir}/" . $modelRevisionClassName . '.php'), $stub);
        }

        $activeModelTraits = [];

        foreach ($activeTraits as $index => $traitIsActive) {
            if ($traitIsActive) {
                !isset($this->modelTraits[$index]) ?: $activeModelTraits[] = $this->modelTraits[$index];
            }
        }

        $activeModelTraitsString = empty($activeModelTraits) ? '' : 'use ' . rtrim(implode(', ', $activeModelTraits), ', ') . ';';

        $activeModelTraitsImports = empty($activeModelTraits) ? '' : "use A17\Twill\Models\Behaviors\\" . implode(";\nuse A17\Twill\Models\Behaviors\\", $activeModelTraits) . ";";

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

        twill_put_stub(twill_path("{$modelsDir}/" . $modelName . '.php'), $stub);

        $this->info("Models created successfully! Fill your fillables!");
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
        $modelsDir = $this->isCapsule ? $this->capsule['repositories_dir'] : 'Repositories';

        $modelClass = $this->isCapsule ? $this->capsule['model'] : "App\Models\\{$this->capsule['singular']}";

        $this->makeTwillDirectory($modelsDir);

        $repositoryClassName = $modelName . 'Repository';

        $activeRepositoryTraits = [];

        foreach ($activeTraits as $index => $traitIsActive) {
            if ($traitIsActive) {
                !isset($this->repositoryTraits[$index]) ?: $activeRepositoryTraits[] = $this->repositoryTraits[$index];
            }
        }

        $activeRepositoryTraitsString = empty($activeRepositoryTraits) ? '' : 'use ' . (empty($activeRepositoryTraits) ? "" : rtrim(implode(', ', $activeRepositoryTraits), ', ') . ';');

        $activeRepositoryTraitsImports = empty($activeRepositoryTraits) ? '' : "use A17\Twill\Repositories\Behaviors\\" . implode(";\nuse A17\Twill\Repositories\Behaviors\\", $activeRepositoryTraits) . ";";

        $stub = str_replace(
            ['{{repositoryClassName}}', '{{modelName}}', '{{repositoryTraits}}', '{{repositoryImports}}', '{{namespace}}', '{{modelClass}}', '{{baseRepository}}'],
            [$repositoryClassName, $modelName, $activeRepositoryTraitsString, $activeRepositoryTraitsImports, $this->namespace('repositories', 'Repositories'), $modelClass, config('twill.base_repository')],
            $this->files->get(__DIR__ . '/stubs/repository.stub')
        );

        twill_put_stub(twill_path("{$modelsDir}/" . $repositoryClassName . '.php'), $stub);

        $this->info("Repository created successfully! Control all the things!");
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

        $dir = $this->isCapsule ? $this->capsule['controllers_dir'] : 'Http/Controllers/Admin';

        $this->makeTwillDirectory($dir);

        $stub = str_replace(
            ['{{moduleName}}', '{{controllerClassName}}', '{{namespace}}', '{{baseController}}'],
            [$moduleName, $controllerClassName, $this->namespace('controllers', 'Http\Controllers\Admin'), config('twill.base_controller')],
            $this->files->get(__DIR__ . '/stubs/controller.stub')
        );

        if ($this->sluggable) {
            $stub = preg_replace('/{{!hasSlug}}[\s\S]+?{{\/!hasSlug}}/', '', $stub);
        } else {
            $stub = str_replace(['{{!hasSlug}}', '{{/!hasSlug}}'], '', $stub);
        }

        twill_put_stub(twill_path("{$dir}/" . $controllerClassName . '.php'), $stub);

        $this->info("Controller created successfully! Define your index/browser/form endpoints options!");
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
        $dir = $this->isCapsule ? $this->capsule['requests_dir'] : 'Http/Requests/Admin';

        $this->makeTwillDirectory($dir);

        $requestClassName = $modelName . 'Request';

        $stub = str_replace(
            ['{{requestClassName}}', '{{namespace}}', '{{baseRequest}}'],
            [$requestClassName, $this->namespace('requests', 'Http\Requests\Admin'), config('twill.base_request')],
            $this->files->get(__DIR__ . '/stubs/request.stub')
        );

        twill_put_stub(twill_path("{$dir}/" . $requestClassName . '.php'), $stub);

        $this->info("Form request created successfully! Add some validation rules!");
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

        twill_put_stub($viewsPath . '/form.blade.php', $this->files->get(__DIR__ . '/stubs/' . $formView . '.blade.stub'));

        $this->info("Form view created successfully! Include your form fields using @formField directives!");
    }

    /**
     * Creates a basic routes file for the Capsule.
     *
     * @param string $moduleName
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function createRoutes()
    {
        $this->makeDir($this->capsule['routes_file']);

        $contents = str_replace(
            '{{moduleName}}',
            $this->capsule['module'],
            $this->files->get(__DIR__ . '/stubs/routes_admin.stub')
        );

        twill_put_stub($this->capsule['routes_file'], $contents);

        $this->info("Routes file created successfully!");
    }

    /**
     * Creates a new module database seed file.
     *
     * @param string $moduleName
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function createSeed($moduleName = 'items')
    {
        $this->makeTwillDirectory($this->capsule['seeds_psr4_path']);

        $stub = $this->files->get(__DIR__ . '/stubs/database_seeder.stub');

        $stub = str_replace('{moduleName}', $this->capsule['plural'], $stub);

        $this->files->put("{$this->capsule['seeds_psr4_path']}/DatabaseSeeder.php", $stub);

        $this->info("Seed created successfully!");
    }

    private function checkOption($option)
    {
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
        ];

        return 'yes' === $this->choice($questions[$option], ['no', 'yes'], $this->defaultsAnswserToNo ? 0 : 1);
    }

    public function createCapsulePath($moduleName, $modelName)
    {
        if (!$this->isCapsule) {
            $this->moduleBasePath = base_path();

            return;
        }

        $this->checkCapsuleDirectory(
            $this->moduleBasePath = config('twill.capsules.path') . "/{$moduleName}"
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
        if (!$this->isCapsule) {
            return database_path($path);
        }

        return "{$this->moduleBasePath}/database" . (filled($path) ? "/{$path}" : '');
    }

    public function makeDir($dir)
    {
        $info = pathinfo($dir);

        $dir = isset($info['extension']) ? $info['dirname'] : $dir;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!is_dir($dir)) {
            $this->info("It wasn't possible to create capsule directory {$dir}");

            die;
        }
    }

    public function makeTwillDirectory($path)
    {
        make_twill_directory($path);
    }

    public function namespace ($type, $suffix, $class = null) {
        $class = (filled($class) ? "\\$class" : '');

        if (!$this->isCapsule) {
            return "App\\{$suffix}{$class}";
        }

        return $this->capsule[$type] . $class;
    }

    public function viewPath($moduleName)
    {
        if (!$this->isCapsule) {
            return $viewsPath = $this->config->get('view.paths')[0] . '/admin/' . $moduleName;
        }

        $this->makeDir($dir = "{$this->moduleBasePath}/resources/views/admin");

        return $dir;
    }
}
