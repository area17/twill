<?php

namespace A17\Twill\Services\Capsules;

class Manager
{
    use HasCapsules;

    function capsuleNamespace($capsuleName, $type = null)
    {
        $base = config('twill.capsules.namespaces.base');

        $type = config("twill.capsules.namespaces.{$type}");

        return "{$base}\\{$capsuleName}" . (filled($type) ? "\\{$type}" : '');
    }
}
