<?php

namespace A17\Twill\Facades;

use A17\Twill\Helpers\Capsule;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string capsuleNamespace(string $capsuleName, string $type = null)
 * @method static string capsuleNamespaceToPath(string $namespace, string $capsuleNamespace, string $rootPath)
 * @method static Capsule registerPackageCapsule(string $name, string $namespace, string $path, string $singular = null, bool $enabled = true, bool $automaticNavigation = true)
 */
class TwillCapsules extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \A17\Twill\TwillCapsules::class;
    }
}
