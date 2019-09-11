<?php

namespace A17\Twill\Commands;

use Illuminate\Config\Repository as Config;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class ModuleMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:module {moduleName}
        {--B|hasBlocks}
        {--T|hasTranslation}
        {--S|hasSlug}
        {--M|hasMedias}
        {--F|hasFiles}
        {--P|hasPosition}
        {--R|hasRevisions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CMS Module';

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

        $this->modelTraits = ['HasBlocks', 'HasTranslation', 'HasSlug', 'HasMedias', 'HasFiles', 'HasRevisions', 'HasPosition'];
        $this->repositoryTraits = ['HandleBlocks', 'HandleTranslations', 'HandleSlugs', 'HandleMedias', 'HandleFiles', 'HandleRevisions'];
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $moduleName = Str::plural(lcfirst($this->argument('moduleName')));

        $blockable = $this->option('hasBlocks') ?? false;
        $translatable = $this->option('hasTranslation') ?? false;
        $sluggable = $this->option('hasSlug') ?? false;
        $mediable = $this->option('hasMedias') ?? false;
        $fileable = $this->option('hasFiles') ?? false;
        $sortable = $this->option('hasPosition') ?? false;
        $revisionable = $this->option('hasRevisions') ?? false;

        $activeTraits = [$blockable, $translatable, $sluggable, $mediable, $fileable, $revisionable, $sortable];

        $modelName = Str::studly(Str::singular($moduleName));

        $this->createMigration($moduleName);
        $this->createModels($modelName, $translatable, $sluggable, $sortable, $revisionable, $activeTraits);
        $this->createRepository($modelName, $activeTraits);
        $this->createController($moduleName, $modelName);
        $this->createRequest($modelName);
        $this->createViews($moduleName, $translatable);

        $this->info("Add Route::module('{$moduleName}'); to your admin routes file.");
        $this->info("Setup a new CMS menu item in config/twill-navigation.php:");

        $navTitle = Str::studly($moduleName);
        $this->info("
            '{$moduleName}' => [
                'title' => '{$navTitle}',
                'module' => true
            ]
        ");

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

        if (!count(glob(database_path('migrations/*' . $migrationName . '.php')))) {
            $migrationPath = $this->laravel->databasePath() . '/migrations';

            $fullPath = $this->laravel['migration.creator']->create($migrationName, $migrationPath);

            $stub = str_replace(
                ['{{table}}', '{{singularTableName}}', '{{tableClassName}}'],
                [$table, Str::singular($table), $tableClassName],
                $this->files->get(__DIR__ . '/stubs/migration.stub')
            );

            $this->files->put($fullPath, $stub);

            $this->info("Migration created successfully! Add some fields!");
        }
    }

    /**
     * Creates new model class files for the given model name and traits.
     *
     * @param string $modelName
     * @param bool $translatable
     * @param bool $sluggable
     * @param bool $sortable
     * @param bool $revisionable
     * @param array $activeTraits
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function createModels($modelName = 'Item', $translatable = false, $sluggable = false, $sortable = false, $revisionable = false, $activeTraits = [])
    {
        if (!$this->files->isDirectory(twill_path('Models'))) {
            $this->files->makeDirectory(twill_path('Models'));
        }

        if ($translatable) {
            if (!$this->files->isDirectory(twill_path('Models/Translations'))) {
                $this->files->makeDirectory(twill_path('Models/Translations'));
            }

            $modelTranslationClassName = $modelName . 'Translation';

            $stub = str_replace('{{modelTranslationClassName}}', $modelTranslationClassName, $this->files->get(__DIR__ . '/stubs/model_translation.stub'));

            $this->files->put(twill_path('Models/Translations/' . $modelTranslationClassName . '.php'), $stub);
        }

        if ($sluggable) {
            if (!$this->files->isDirectory(twill_path('Models/Slugs'))) {
                $this->files->makeDirectory(twill_path('Models/Slugs'));
            }

            $modelSlugClassName = $modelName . 'Slug';

            $stub = str_replace(['{{modelSlugClassName}}', '{{modelName}}'], [$modelSlugClassName, Str::snake($modelName)], $this->files->get(__DIR__ . '/stubs/model_slug.stub'));

            $this->files->put(twill_path('Models/Slugs/' . $modelSlugClassName . '.php'), $stub);
        }

        if ($revisionable) {
            if (!$this->files->isDirectory(twill_path('Models/Revisions'))) {
                $this->files->makeDirectory(twill_path('Models/Revisions'));
            }

            $modelRevisionClassName = $modelName . 'Revision';

            $stub = str_replace(['{{modelRevisionClassName}}', '{{modelName}}'], [$modelRevisionClassName, Str::snake($modelName)], $this->files->get(__DIR__ . '/stubs/model_revision.stub'));

            $this->files->put(twill_path('Models/Revisions/' . $modelRevisionClassName . '.php'), $stub);
        }

        $modelClassName = $modelName;

        $activeModelTraits = [];

        foreach ($activeTraits as $index => $traitIsActive) {
            if ($traitIsActive) {
                !isset($this->modelTraits[$index]) ?: $activeModelTraits[] = $this->modelTraits[$index];
            }
        }

        $activeModelTraitsString = empty($activeModelTraits) ? '' : 'use ' . rtrim(implode(', ', $activeModelTraits), ', ') . ';';

        $activeModelTraitsImports = empty($activeModelTraits) ? '' : "use A17\Twill\Models\Behaviors\\" . implode(";\nuse A17\Twill\Models\Behaviors\\", $activeModelTraits) . ";";

        $activeModelImplements = $sortable ? 'implements Sortable' : '';

        if ($sortable) {
            $activeModelTraitsImports .= "\nuse A17\Twill\Models\Behaviors\Sortable;";
        }

        $stub = str_replace(['{{modelClassName}}', '{{modelTraits}}', '{{modelImports}}', '{{modelImplements}}'], [$modelClassName, $activeModelTraitsString, $activeModelTraitsImports, $activeModelImplements], $this->files->get(__DIR__ . '/stubs/model.stub'));

        $this->files->put(twill_path('Models/' . $modelClassName . '.php'), $stub);

        $this->info("Models created successfully! Fill your fillables!");
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
        if (!$this->files->isDirectory(twill_path('Repositories'))) {
            $this->files->makeDirectory(twill_path('Repositories'));
        }

        $repositoryClassName = $modelName . 'Repository';

        $activeRepositoryTraits = [];

        foreach ($activeTraits as $index => $traitIsActive) {
            if ($traitIsActive) {
                !isset($this->repositoryTraits[$index]) ?: $activeRepositoryTraits[] = $this->repositoryTraits[$index];
            }
        }

        $activeRepositoryTraitsString = empty($activeRepositoryTraits) ? '' : 'use ' . (empty($activeRepositoryTraits) ? "" : rtrim(implode(', ', $activeRepositoryTraits), ', ') . ';');

        $activeRepositoryTraitsImports = empty($activeRepositoryTraits) ? '' : "use A17\Twill\Repositories\Behaviors\\" . implode(";\nuse A17\Twill\Repositories\Behaviors\\", $activeRepositoryTraits) . ";";

        $stub = str_replace(['{{repositoryClassName}}', '{{modelName}}', '{{repositoryTraits}}', '{{repositoryImports}}'], [$repositoryClassName, $modelName, $activeRepositoryTraitsString, $activeRepositoryTraitsImports], $this->files->get(__DIR__ . '/stubs/repository.stub'));

        $this->files->put(twill_path('Repositories/' . $repositoryClassName . '.php'), $stub);

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
        if (!$this->files->isDirectory(twill_path('Http/Controllers/Admin'))) {
            $this->files->makeDirectory(twill_path('Http/Controllers/Admin'));
        }

        $controllerClassName = $modelName . 'Controller';

        $stub = str_replace(
            ['{{moduleName}}', '{{controllerClassName}}'],
            [$moduleName, $controllerClassName],
            $this->files->get(__DIR__ . '/stubs/controller.stub')
        );

        $this->files->put(twill_path('Http/Controllers/Admin/' . $controllerClassName . '.php'), $stub);

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
        if (!$this->files->isDirectory(twill_path('Http/Requests/Admin'))) {
            $this->files->makeDirectory(twill_path('Http/Requests/Admin'), 0755, true);
        }

        $requestClassName = $modelName . 'Request';

        $stub = str_replace('{{requestClassName}}', $requestClassName, $this->files->get(__DIR__ . '/stubs/request.stub'));

        $this->files->put(twill_path('Http/Requests/Admin/' . $requestClassName . '.php'), $stub);

        $this->info("Form request created successfully! Add some validation rules!");
    }

    /**
     * Creates appropriate module Blade view files.
     *
     * @param string $moduleName
     * @param bool $translatable
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function createViews($moduleName = 'items', $translatable = false)
    {
        $viewsPath = $this->config->get('view.paths')[0] . '/admin/' . $moduleName;

        if (!$this->files->isDirectory($viewsPath)) {
            $this->files->makeDirectory($viewsPath, 0755, true);
        }

        $formView = $translatable ? 'form_translatable' : 'form';

        $this->files->put($viewsPath . '/form.blade.php', $this->files->get(__DIR__ . '/stubs/' . $formView . '.blade.stub'));

        $this->info("Form view created successfully! Include your form fields using @formField directives!");
    }
}
