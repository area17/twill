<?php

namespace A17\Twill;

use A17\Twill\Exceptions\NoCapsuleFoundException;
use A17\Twill\Helpers\Capsule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TwillCapsules
{
    /**
     * @var \A17\Twill\Helpers\Capsule[]
     */
    public array $registeredCapsules = [];

    public function registerPackageCapsule(
        string $name,
        string $namespace,
        string $path,
        string $singular = null,
        bool $enabled = true,
        bool $automaticNavigation = true
    ): Capsule {
        $capsule = new Capsule($name, $namespace, $path, $singular, $enabled, true, $automaticNavigation);

        $this->registerCapsule($capsule);

        return $this->registeredCapsules[$name];
    }

    public function registerCapsule(Capsule $capsule): void
    {
        $this->registeredCapsules[$capsule->name] = $capsule;
    }

    /**
     * Generates a non package capsule object.
     */
    public function makeProjectCapsule(string $name): Capsule
    {
        return new Capsule(
            $name,
            $this->capsuleNamespace($name),
            config('twill.capsules.path') . DIRECTORY_SEPARATOR . $name
        );
    }

    /**
     * @throws \A17\Twill\Exceptions\NoCapsuleFoundException
     */
    public function getCapsuleForModule(string $module): Capsule
    {
        $capsule = $this->getRegisteredCapsules()->first(function (Capsule $capsule) use ($module) {
            return $capsule->getModule() === $module;
        });

        if (!$capsule) {
            throw new NoCapsuleFoundException($module);
        }

        return $capsule;
    }

    /**
     * @throws \A17\Twill\Exceptions\NoCapsuleFoundException
     */
    public function getCapsuleForModel(string $model): Capsule
    {
        $capsule = $this->getRegisteredCapsules()->first(function (Capsule $capsule) use ($model) {
            return $capsule->getSingular() === $model;
        });

        if (!$capsule) {
            throw new NoCapsuleFoundException($model);
        }

        return $capsule;
    }

    /**
     * @return Collection<Capsule>
     */
    public function getRegisteredCapsules(): Collection
    {
        $this->loadProjectCapsules();

        return collect($this->registeredCapsules);
    }

    public function loadProjectCapsules(): void
    {
        $path = config('twill.capsules.path');

        $list = collect(config('twill.capsules.list'));

        $list
            ->where('enabled', true)
            ->filter(function ($capsule) {
                return !isset($this->registeredCapsules[$capsule['name']]);
            })
            ->map(function ($capsule) use ($path) {
                $this->registerCapsule(
                    new Capsule(
                        $capsule['name'],
                        $this->capsuleNamespace($capsule['name']),
                        $path . DIRECTORY_SEPARATOR . $capsule['name'],
                        $capsule['singular'] ?? null,
                        $capsule['enabled'] ?? true
                    )
                );
            });
    }

    public function capsuleNamespace($capsuleName, $type = null): string
    {
        // @todo: Read from capsules to get this data.
        $base = config('twill.capsules.namespaces.base');

        $type = config("twill.capsules.namespaces.$type");

        return "$base\\$capsuleName" . (filled($type) ? "\\$type" : '');
    }

    public function capsuleNamespaceToPath(
        $namespace,
        $capsuleNamespace,
        $rootPath
    ): string {
        $namespace = Str::after($namespace, $capsuleNamespace . '\\');

        return $rootPath . DIRECTORY_SEPARATOR . $this->getProjectCapsulesSubdirectory() . str_replace(
            '\\',
            DIRECTORY_SEPARATOR,
            $namespace
        );
    }

    public function getProjectCapsulesPath(): string
    {
        return config('twill.capsules.path') . $this->getProjectCapsulesSubdirectory();
    }

    private function getProjectCapsulesSubdirectory(): string
    {
        $subdirectory = config('twill.capsules.namespaces.subdir');

        return filled($subdirectory) ? $subdirectory . DIRECTORY_SEPARATOR : '';
    }

    public function getAutoloader()
    {
        return app()->bound('autoloader')
            ? app('autoloader')
            : require base_path('vendor/autoload.php');
    }
}
