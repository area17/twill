<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use \Illuminate\Support\Str;

if (!function_exists('dumpUsableSqlQuery')) {
    function dumpUsableSqlQuery($query)
    {
        dd(vsprintf(str_replace('?', '%s', $query->toSql()), array_map(function ($binding) {
            return is_numeric($binding) ? $binding : "'{$binding}'";
        }, $query->getBindings())));
    }
}

if (!function_exists('classUsesDeep')) {
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
        while (!empty($traitsToSearch)) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        }

        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return array_unique($traits);
    }
}

if (!function_exists('classHasTrait')) {
    /**
     * @param mixed $class
     * @param string $trait
     * @return bool
     */
    function classHasTrait($class, $trait)
    {
        $traits = classUsesDeep($class);

        if (in_array($trait, array_keys($traits))) {
            return true;
        }

        return false;
    }
}

if (!function_exists('getFormFieldsValue')) {
    /**
     * @param array $formFields
     * @param string $name
     * @return mixed
     */
    function getFormFieldsValue($formFields, $name)
    {
        return Arr::get($formFields, str_replace(']', '', str_replace('[', '.', $name)), '');
    }
}

if (!function_exists('fireCmsEvent')) {
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

if (!function_exists('twill_path')) {
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
        preg_match('/(\w*)\W?(.*)/', config('twill.namespace'), $namespaceParts);

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

if (!function_exists('make_twill_directory')) {
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

        if (!$fs->isDirectory($path)) {
            $fs->makeDirectory($path, 0755, $recursive);
        }
    }
}

if (!function_exists('twill_put_stub')) {
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

        if (!$fs->exists($path)) {
            $fs->put($path, $stub);
        }
    }
}

if (!function_exists('fix_directory_separator')) {
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
