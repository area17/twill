<?php

namespace A17\Twill;

use A17\Twill\Facades\TwillBlocks;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class TwillPackageServiceProvider extends ServiceProvider
{
    protected $autoRegisterCapsules = true;

    public function boot(): void
    {
        if ($this->autoRegisterCapsules) {
            $this->registerCapsules('Twill/Capsules');
        }
    }

    protected function registerCapsule(string $name): void
    {
        $namespace = $this->getCapsuleNamespace();

        $namespace .= '\\Twill\\Capsules\\' . $name;

        $dir = $this->getPackageDirectory() . '/src/Twill/Capsules/' . $name;

        \A17\Twill\Facades\TwillCapsules::registerPackageCapsule($name, $namespace, $dir);
    }

    protected function registerCapsules(string $directory): void
    {
        $storage = Storage::build([
            'driver' => 'local',
            'root' => $this->getPackageDirectory() . '/src/' . $directory,
        ]);

        foreach ($storage->directories() as $capsuleName) {
            $this->registerCapsule($capsuleName);
        }
    }

    protected function getClassName(): string
    {
        $provider = explode('\\', get_class($this));

        return array_pop($provider);
    }

    protected function getCapsuleNamespace(): string
    {
        $provider = explode('\\', get_class($this));
        array_pop($provider);

        return implode('\\', $provider);
    }

    protected function getPackageDirectory(): string
    {
        $class = new ReflectionClass(get_class($this));

        $path = Str::replaceLast('/' . $this->getClassName() . '.php', '', $class->getFileName());

        if (Str::endsWith($path, '/src')) {
            $path = Str::replaceLast('/src', '', $path);
        }

        return $path;
    }

    protected function registerVueComponentsDirectory($path)
    {
        $this->publishes(
            [$path => resource_path(config('twill.vendor_components_resource_path'))],
            'components'
        );
    }

    protected function registerBlocksDirectory($path)
    {
        TwillBlocks::registerPackageBlocksDirectory($path);
    }

    protected function registerRepeatersDirectory($path)
    {
        TwillBlocks::registerPackageRepeatersDirectory($path);
    }
}
