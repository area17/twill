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
        $this->addServiceProvider();
        $this->replaceExceptionsHandler();
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

    private function addServiceProvider()
    {
        $fileToReplace = base_path('config/app.php');
        $lineToReplace = 'A17\CmsToolkit\CmsToolkitInstallServiceProvider::class,';
        $newLine = 'A17\CmsToolkit\CmsToolkitServiceProvider::class,';
        $this->replaceAndSave($fileToReplace, $lineToReplace, $newLine);
    }

    private function replaceAndSave($oldFile, $search, $replace, $newFile = null)
    {
        $newFile = ($newFile == null) ? $oldFile : $newFile;
        $file = $this->files->get($oldFile);
        $replacing = str_replace($search, $replace, $file);
        $this->files->put($newFile, $replacing);
    }

    private function replaceExceptionsHandler()
    {
        $exceptionsPath = app_path('Exceptions');

        if (!$this->files->exists($exceptionsPath)) {
            $this->files->makeDirectory($exceptionsPath, 0755, true);
        }

        $stub = $this->files->get(__DIR__ . '/stubs/Handler.stub');

        $this->files->put($exceptionsPath . '/Handler.php', $stub);
    }
}
