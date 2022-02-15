<?php

namespace A17\Twill;

use A17\Twill\Exceptions\CapsuleWithNameAlreadyExistsException;
use A17\Twill\Exceptions\NoCapsuleFoundException;
use A17\Twill\Helpers\Capsule;
use Illuminate\Support\Collection;

class TwillCapsules
{
    /**
     * @var \A17\Twill\Helpers\Capsule[]
     */
    public static $registeredCapsules = [];

    /**
     * @var bool
     */
    public static $isLoaded = false;

    /**
     * @throws \A17\Twill\Exceptions\CapsuleWithNameAlreadyExistsException
     */
    public function registerPackageCapsule(
        string $name,
        string $namespace,
        string $path,
        string $singular = null,
        bool $enabled = true
    ): Capsule {
        if (isset(self::$registeredCapsules[$name])) {
            throw new CapsuleWithNameAlreadyExistsException();
        }

        self::$registeredCapsules[$name] = new Capsule($name, $namespace, $path, $singular, $enabled, true);

        return self::$registeredCapsules[$name];
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
     * @return Capsule[]
     */
    public function getRegisteredCapsules(): Collection
    {
        $this->loadProjectCapsules();

        return collect(self::$registeredCapsules);
    }

    public function loadProjectCapsules(): void
    {
        $path = config('twill.capsules.path');

        $list = collect(config('twill.capsules.list'));

        if (!self::$isLoaded) {
            $list
                ->where('enabled', true)
                ->map(function ($capsule) use ($path) {
                    self::$registeredCapsules[$capsule['name']] = new Capsule(
                        $capsule['name'],
                        $this->capsuleNamespace($capsule['name']),
                        $path . '/' . $capsule['name'],
                        $capsule['singular'] ?? null,
                        $capsule['enabled'] ?? true
                    );
                });
        }
    }

    private function capsuleNamespace($capsuleName, $type = null): string
    {
        $base = config('twill.capsules.namespaces.base');

        $type = config("twill.capsules.namespaces.$type");

        return "$base\\$capsuleName" . (filled($type) ? "\\$type" : '');
    }

    public function getAutoloader() {
        return app()->bound('autoloader')
            ? app('autoloader')
            : require base_path('vendor/autoload.php');
    }
}
