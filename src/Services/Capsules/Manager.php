<?php

namespace A17\Twill\Services\Capsules;

use Composer\InstalledVersions;

class Manager
{
    use HasCapsules;

    function capsuleNamespace($capsule, $type = null)
    {
        $type = config("twill.capsules.namespaces.{$type}");

        if ($capsule['composer'] === true) {
            $composerJsonPath = InstalledVersions::getInstallPath($capsule['fullName']) . '/composer.json';
            $composer = json_decode(file_get_contents($composerJsonPath), true);

            $baseNamespace = array_keys($composer['autoload']['psr-4'])[0];
            return rtrim($baseNamespace, '\\') . (filled($type) ? "\\{$type}" : '');
        }

        $base = config('twill.capsules.namespaces.base');


        return "{$base}\\{$capsule['name']}" . (filled($type) ? "\\{$type}" : '');
    }
}
