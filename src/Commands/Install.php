<?php

namespace A17\CmsToolkit\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    protected $signature = 'cms-toolkit:install';

    protected $description = 'Install the CMS Toolkit into a default Laravel application';

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

        if (!File::exists($routesPath)) {
            File::makeDirectory($routesPath, 0755, true);
        }

        $stub = $this->files->get(__DIR__ . '/stubs/admin.stub');

        $this->files->put($routesPath . '/admin.php', $stub);

    }

    private function replaceExceptionsHandler()
    {
        $exceptionsPath = app_path('Exceptions');

        if (!File::exists($exceptionsPath)) {
            File::makeDirectory($exceptionsPath, 0755, true);
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
