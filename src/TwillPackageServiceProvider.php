<?php

namespace A17\Twill;

use A17\Twill\Facades\TwillBlocks;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class TwillPackageServiceProvider extends ServiceProvider
{
    protected bool $autoRegisterCapsules = true;

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
        $provider = explode('\\', $this::class);

        return array_pop($provider);
    }

    protected function getCapsuleNamespace(): string
    {
        $provider = explode('\\', $this::class);
        array_pop($provider);

        return implode('\\', $provider);
    }

    protected function getPackageDirectory(): string
    {
        $class = new ReflectionClass($this::class);

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

    /**
     * Register a blocks directory.
     *
     * If a namespace is provided for the render, Twill will assume it to be under:
     *   NAMESPACE::site.blocks.BLOCK-NAME
     *
     * Given `$this->loadViewsFrom(__DIR__ . '/../resources/views/site/blocks', 'package-name');`
     *
     * So if you have a block called "example" and you want your package to provide the
     * preview. Your file should be in `resources\views\site\blocks\example.blade.php`.
     *
     * To make sure the end user can override the views, you should make them publishable:
     * ```
     *   $this->publishes([
     *        __DIR__ . '/../resources/views' => resource_path('views/vendor/PACKAGE-NAME'),
     *   ]);
     * ```
     */
    protected function registerBlocksDirectory($path, string $renderNamespace = null)
    {
        TwillBlocks::registerPackageBlocksDirectory($path, $renderNamespace);
    }

    protected function registerRepeatersDirectory($path, string $renderNamespace = null)
    {
        TwillBlocks::registerPackageRepeatersDirectory($path, $renderNamespace);
    }
}
