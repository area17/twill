<?php

namespace A17\Twill\Commands;

use Illuminate\Foundation\Console\ComponentMakeCommand;
use Illuminate\Support\Str;

class GenerateBlockComponent extends ComponentMakeCommand
{
    protected $signature = 'twill:make:componentBlock {name : class name of the block} {namespace? : Twill/Blocks/ by default, this namespace is relative to the App/View namespace} {--force}';
    protected $description = 'Generates a twill block as a component';

    public function handle(): bool
    {
        $namespace = $this->argument('namespace') ?? 'Twill/Blocks/';
        if (! Str::contains($this->argument('name'), ['\\', '/'])) {
            $parts = Str::ucsplit(Str::studly(Str::replace('.', '_', $this->argument('name'))));
            $blockName = $namespace . implode('\\', $parts);
        } else {
            $blockName = $namespace . $this->argument('name');
        }

        $className = str_replace('/', '\\', 'App\View\Components\\' . $blockName);
        $classPath = $this->getPath($className);
        $viewPath = $this->viewPath(
            str_replace('.', '/', 'components.' . $this->getViewFromName($blockName)) . '.blade.php'
        );

        $class = str_replace(
            '{{ view }}',
            'view(\'components.' . $this->getViewFromName($blockName) . '\')',
            $this->buildClass($blockName)
        );

        if ($this->writeClassFile($classPath, $class)) {
            $this->info('Class written to ' . $classPath);
        }
        if ($this->writeViewFile($viewPath)) {
            $this->info('View written to ' . $viewPath);
        }

        return true;
    }

    protected function getStub()
    {
        return __DIR__ . '/stubs/blockComponent/component.php.stub';
    }

    protected function buildClass($name): string
    {
        $name = str_replace('/', '\\', 'App\View\Components\\' . $name);
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    protected function getViewFromName(string $name): string
    {
        $name = str_replace('\\', '/', $name);

        return collect(explode('/', $name))
            ->map(function ($part) {
                return Str::kebab($part);
            })
            ->implode('.');
    }

    private function writeViewFile(string $viewPath): bool
    {
        if (! $this->files->isDirectory(dirname($viewPath))) {
            $this->files->makeDirectory(dirname($viewPath), 0777, true, true);
        }

        if ($this->files->exists($viewPath) && ! $this->option('force')) {
            $this->components->error('View already exists.');

            return false;
        }

        file_put_contents(
            $viewPath,
            file_get_contents(__DIR__ . '/stubs/blockComponent/view.blade.php')
        );

        return true;
    }

    private function writeClassFile(string $classPath, string $class): bool
    {
        if (! $this->files->isDirectory(dirname($classPath))) {
            $this->files->makeDirectory(dirname($classPath), 0777, true, true);
        }

        if ($this->files->exists($classPath) && ! $this->option('force')) {
            $this->components->error('Class already exists.');

            return false;
        }

        file_put_contents(
            $classPath,
            $class
        );

        return true;
    }
}
