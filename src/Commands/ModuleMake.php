<?php

namespace A17\CmsToolkit\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class ModuleMake extends Command
{
    protected $signature = 'make:module {moduleName} {--T|translatable}';

    protected $description = 'Create a new CMS Module';

    protected $files;

    protected $composer;

    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    public function fire()
    {
        $moduleName = $this->argument('moduleName');
        $translatable = $this->option('translatable') ?? false;

        $modelName = Str::studly(Str::singular($moduleName));

        $this->createMigration($moduleName);
        $this->createModels($modelName, $translatable);
        $this->createRepository($modelName);
        $this->createController($moduleName, $modelName);
        $this->createRequest($modelName);
        $this->createViews($moduleName);

        $this->info("\nStart by filling in the migration and models.");
        $this->info("Add Route::module('{$moduleName}'); to your admin routes file.");
        $this->info("Setup a new CMS menu item in config/cms-navigation.php.");
        $this->info("Setup your index and form views.");
        $this->info("Enjoy.");

        $this->composer->dumpAutoloads();
    }

    private function createMigration($moduleName = 'items')
    {
        $table = Str::snake($moduleName);

        $migrationName = 'create_' . $table . '_tables';

        $migrationPath = $this->laravel->databasePath() . '/migrations';

        $fullPath = $this->laravel['migration.creator']->create($migrationName, $migrationPath);

        $stub = str_replace(
            ['{{table}}', '{{translationTable}}', '{{tableClassName}}'], [$table, Str::singular($table), Str::studly($table)], $this->files->get(__DIR__ . '/stubs/migration.stub')
        );

        $this->files->put($fullPath, $stub);

        $this->info('Migration created successfully! Add some fields!');
    }

    private function createController($moduleName = 'items', $modelName = 'Item')
    {
        if (!$this->files->isDirectory(app_path('Http/Controllers/Admin'))) {
            $this->files->makeDirectory(app_path('Http/Controllers/Admin'));
        }

        $controllerClassName = $modelName . 'Controller';

        $stub = str_replace(
            ['{{moduleName}}', '{{controllerClassName}}'], [$moduleName, $controllerClassName], $this->files->get(__DIR__ . '/stubs/controller.stub')
        );

        $this->files->put(app_path('Http/Controllers/Admin/' . $controllerClassName . '.php'), $stub);

        $this->info('Controller created successfully! Add your module name, index and form data!');
    }

    private function createRequest($modelName = 'Item')
    {
        if (!$this->files->isDirectory(app_path('Http/Requests/Admin'))) {
            $this->files->makeDirectory(app_path('Http/Requests'));
            $this->files->makeDirectory(app_path('Http/Requests/Admin'));
        }

        $requestClassName = $modelName . 'Request';

        $stub = str_replace('{{requestClassName}}', $requestClassName, $this->files->get(__DIR__ . '/stubs/request.stub'));

        $this->files->put(app_path('Http/Requests/Admin/' . $requestClassName . '.php'), $stub);

        $this->info('Form request created successfully! Add some validation rules!');
    }

    private function createModels($modelName = 'Item', $translatable = false)
    {
        if (!$this->files->isDirectory(app_path('Models'))) {
            $this->files->makeDirectory(app_path('Models'));
            $this->files->makeDirectory(app_path('Models/Translations'));
        }

        $modelClassName = $modelName;

        $stub = str_replace('{{modelClassName}}', $modelClassName, $this->files->get(__DIR__ . '/stubs/model.stub'));

        $this->files->put(app_path('Models/' . $modelClassName . '.php'), $stub);

        if ($translatable) {
            $modelTranslationClassName = $modelName . 'Translation';

            $stub = str_replace('{{modelTranslationClassName}}', $modelTranslationClassName, $this->files->get(__DIR__ . '/stubs/model_translation.stub'));

            $this->files->put(app_path('Models/Translations/' . $modelTranslationClassName . '.php'), $stub);

            $this->info('Models created successfully! Fill your fillables!');
        }

        $this->info('Model created successfully! Fill your fillables!');

    }

    private function createRepository($modelName = 'Item')
    {
        if (!$this->files->isDirectory(app_path('Repositories'))) {
            $this->files->makeDirectory(app_path('Repositories'));
        }

        $repositoryClassName = $modelName . 'Repository';

        $stub = str_replace(['{{repositoryClassName}}', '{{modelName}}'], [$repositoryClassName, $modelName], $this->files->get(__DIR__ . '/stubs/repository.stub'));

        $this->files->put(app_path('Repositories/' . $repositoryClassName . '.php'), $stub);

        $this->info('Repository created successfully! Control all the things!');
    }

    private function createViews($moduleName = 'items')
    {
        $viewsPath = config('view.paths')[0] . '/admin/' . $moduleName;

        if (!$this->files->isDirectory($viewsPath)) {
            $this->files->makeDirectory($viewsPath);
        }

        $this->files->put($viewsPath . '/index.blade.php', $this->files->get(__DIR__ . '/stubs/index.blade.stub'));
        $this->files->put($viewsPath . '/form.blade.php', $this->files->get(__DIR__ . '/stubs/form.blade.stub'));

        $this->info('Views created successfully! Customize all the things!');
    }
}
