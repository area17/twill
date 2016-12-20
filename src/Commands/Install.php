<?php

namespace A17\CmsToolkit\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Install extends Command
{
    protected $signature = 'cms-toolkit:install';

    protected $description = 'Install the CMS Toolkit into a default Laravel application';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function fire()
    {
        $this->addRoutesFile();
        $this->replaceExceptionsHandler();
        $this->createSuperAdmin();
        $this->publishAssets();
        $this->publishConfigs();
    }

    private function addRoutesFile()
    {
        $routesPath = base_path('routes');

        if (!$this->files->exists($routesPath)) {
            $this->files->makeDirectory($routesPath, 0755, true);
        }

        $stub = $this->files->get(__DIR__ . '/stubs/admin.stub');

        $this->files->put($routesPath . '/admin.php', $stub);

    }

    private function replaceExceptionsHandler()
    {
        $exceptionsPath = app_path('Exceptions');

        if (!$this->files->exists($exceptionsPath)) {
            $this->files->makeDirectory($exceptionsPath, 0755, true);
        }

        $stub = $this->files->get(__DIR__ . '/stubs/Handler.stub');

        $this->files->put($exceptionsPath . 'Handler.php', $stub);
    }

    private function createSuperAdmin()
    {
        $this->call('cms-toolkit:superadmin');
    }

    private function publishAssets()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\CmsToolkit\CmsToolkitServiceProvider',
            '--tag' => 'assets',
        ]);
    }

    private function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => 'A17\CmsToolkit\CmsToolkitServiceProvider',
            '--tag' => 'config',
        ]);
    }
}
