<?php

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Facades\TwillCapsules;
use A17\Twill\Models\Model;
use A17\Twill\Services\Blocks\Block;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

if (! function_exists('dumpUsableSqlQuery')) {
    function dumpUsableSqlQuery($query)
    {
        dd(vsprintf(str_replace('?', '%s', $query->toSql()), array_map(function ($binding) {
            return is_numeric($binding) ? $binding : "'{$binding}'";
        }, $query->getBindings())));
    }
}

if (! function_exists('getLikeOperator')) {
    function getLikeOperator(): string
    {
        return once(function () {
            if (DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'pgsql') {
                return 'ILIKE';
            }

            return 'LIKE';
        });
    }
}

if (! function_exists('classUsesDeep')) {
    /**
     * @param mixed $class
     * @param bool $autoload
     * @return array
     */
    function classUsesDeep($class, $autoload = true)
    {
        $traits = [];

        // Get traits of all parent classes
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));

        // Get traits of all parent traits
        $traitsToSearch = $traits;
        while (! empty($traitsToSearch)) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        }

        foreach (array_keys($traits) as $trait) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return array_unique($traits);
    }
}

if (! function_exists('classHasTrait')) {
    /**
     * @param mixed $class
     * @param string $trait
     * @return bool
     */
    function classHasTrait($class, $trait)
    {
        $traits = classUsesDeep($class);

        return array_key_exists($trait, $traits);
    }
}

if (! function_exists('getFormFieldsValue')) {
    /**
     * @param array $formFields
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    function getFormFieldsValue($formFields, $name, $default = null)
    {
        return Arr::get($formFields, str_replace(']', '', str_replace('[', '.', $name)), $default ?? '') ?? $default;
    }
}

if (! function_exists('fireCmsEvent')) {
    /**
     * @param string $eventName
     * @param array $input
     * @return void
     */
    function fireCmsEvent($eventName, $input = [])
    {
        $method = method_exists(\Illuminate\Events\Dispatcher::class, 'dispatch') ? 'dispatch' : 'fire';
        Event::$method($eventName, [$eventName, $input]);
    }
}

if (! function_exists('twill_path')) {
    /**
     * @param string $path
     * @return string
     */
    function twill_path($path = '')
    {
        // Is it a full application path?
        if (Str::startsWith($path, base_path())) {
            return $path;
        }

        // Split to separate root namespace
        preg_match('#(\w*)\W?(.*)#', config('twill.namespace'), $namespaceParts);

        $twillBase = app_path(
            fix_directory_separator(
                $namespaceParts[1] == 'App' ? $namespaceParts[2] : $namespaceParts[0]
            )
        ) . '/';

        // Remove base path from path
        if (Str::startsWith($path, $twillBase)) {
            $path = Str::after($path, $twillBase);
        }

        // Namespace App is unchanged in config?
        if ($namespaceParts[0] === 'App') {
            return app_path($path);
        }

        // If it it still starts with App, use the left part, otherwise use the whole namespace
        // This can be a problem for those using a completely different app path for the application
        $left = ($namespaceParts[1] === 'App' ? $namespaceParts[2] : $namespaceParts[0]);

        // Join, fix slashes for the current operating system, and return path
        return app_path(fix_directory_separator(
            $left . (filled($path) ? '\\' . $path : '')
        ));
    }
}

if (! function_exists('make_twill_directory')) {
    /**
     * @param string $path
     * @param bool $recursive
     * @param \Illuminate\Filesystem\Filesystem|null $fs
     */
    function make_twill_directory($path, $recursive = true, $fs = null)
    {
        $fs = filled($fs)
        ? $fs
        : app(Filesystem::class);

        $path = twill_path($path);

        if (! $fs->isDirectory($path)) {
            $fs->makeDirectory($path, 0755, $recursive);
        }
    }
}

if (! function_exists('twill_put_stub')) {
    /**
     * @param string $path
     * @param bool $recursive
     * @param \Illuminate\Filesystem\Filesystem|null $fs
     */
    function twill_put_stub($path, $stub, $fs = null)
    {
        $fs = filled($fs)
        ? $fs
        : app(Filesystem::class);

        $stub = str_replace(
            'namespace App\\',
            sprintf('namespace %s\\', config('twill.namespace')),
            $stub
        );

        $dir = Str::beforeLast($path, DIRECTORY_SEPARATOR);

        $fs->ensureDirectoryExists($dir);

        if (! $fs->exists($path)) {
            $fs->put($path, $stub);
        }
    }
}

if (! function_exists('fix_directory_separator')) {
    /**
     * @param string $path
     * @param bool $recursive
     * @param int $mode
     */
    function fix_directory_separator($path)
    {
        return str_replace(
            '\\',
            DIRECTORY_SEPARATOR,
            $path
        );
    }
}

if (! function_exists('twillModel')) {
    /** @return class-string<Model>|Model It returns a class string but this is for the correct type hints */
    function twillModel($model): string
    {
        return config("twill.models.$model")
            ?? abort(500, "helpers/twillModel: '$model' model is not configured");
    }
}

if (! function_exists('generate_list_of_available_blocks')) {
    /**
     * TODO remove in v4
     * @deprecated use TwillBlocks::generateListOfAvailableBlocks instead
     */
    function generate_list_of_available_blocks(?array $blocks = null, ?array $groups = null, bool $settingsOnly = false, array|callable $excludeBlocks = []): array
    {
        trigger_deprecation('area17/twill', '3.3', __FUNCTION__ . ' is deprecated and will be removed in 4.x, use TwillBlocks::generateListOfAvailableBlocks instead');

        return TwillBlocks::generateListOfAvailableBlocks($blocks, $groups, $settingsOnly, $excludeBlocks)->all();
    }
}

if (! function_exists('capsule_namespace')) {
    /**
     * @deprecated use TwillCapsules::capsuleNamespace instead
     */
    function capsule_namespace($capsuleName, $type = null)
    {
        return TwillCapsules::capsuleNamespace($capsuleName, $type);
    }
}

if (! function_exists('capsule_namespace_to_path')) {
    /**
     * @deprecated use TwillCapsules::capsuleNamespaceToPath instead
     */
    function capsule_namespace_to_path($namespace, $capsuleNamespace, $rootPath)
    {
        return TwillCapsules::capsuleNamespaceToPath($namespace, $capsuleNamespace, $rootPath);
    }
}

if (! function_exists('str_after_last')) {
    /**
     * @todo: In twill 3.x remove and replace with Str::afterlast
     */
    function str_after_last($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        $position = strrpos($subject, (string) $search);

        if ($position === false) {
            return $subject;
        }

        return substr($subject, $position + strlen($search));
    }
}
