<?php

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Facades\TwillCapsules;
use A17\Twill\Services\Blocks\Block;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

if (! function_exists('dumpUsableSqlQuery')) {
    /**
     * @return never
     */
    function dumpUsableSqlQuery($query)
    {
        dd(vsprintf(str_replace('?', '%s', $query->toSql()), array_map(function ($binding) {
            return is_numeric($binding) ? $binding : sprintf('\'%s\'', $binding);
        }, $query->getBindings())));
    }
}

if (! function_exists('classUsesDeep')) {
    /**
     * @param mixed $class
     */
    function classUsesDeep($class, bool $autoload = true): array
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
     */
    function classHasTrait($class, string $trait): bool
    {
        $traits = classUsesDeep($class);
        return array_key_exists($trait, $traits);
    }
}

if (! function_exists('getFormFieldsValue')) {
    /**
     * @param mixed $default
     * @return mixed
     */
    function getFormFieldsValue(array $formFields, string $name, $default = null)
    {
        return Arr::get($formFields, str_replace(']', '', str_replace('[', '.', $name)), $default ?? '') ?? $default;
    }
}

if (! function_exists('fireCmsEvent')) {
    function fireCmsEvent(string $eventName, array $input = []): void
    {
        $method = method_exists(\Illuminate\Events\Dispatcher::class, 'dispatch') ? 'dispatch' : 'fire';
        Event::$method($eventName, [$eventName, $input]);
    }
}

if (! function_exists('twill_path')) {
    function twill_path(string $path = ''): string
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
     * @param \Illuminate\Filesystem\Filesystem|null $fs
     */
    function make_twill_directory(string $path, bool $recursive = true, $fs = null)
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
     * @param bool $recursive
     * @param \Illuminate\Filesystem\Filesystem|null $fs
     */
    function twill_put_stub(string $path, $stub, $fs = null)
    {
        $fs = filled($fs)
        ? $fs
        : app(Filesystem::class);

        $stub = str_replace(
            'namespace App\\',
            sprintf('namespace %s\\', config('twill.namespace')),
            $stub
        );

        if (! $fs->exists($path)) {
            $fs->put($path, $stub);
        }
    }
}

if (! function_exists('fix_directory_separator')) {
    /**
     * @param bool $recursive
     * @param int $mode
     */
    function fix_directory_separator(string $path)
    {
        return str_replace(
            '\\',
            DIRECTORY_SEPARATOR,
            $path
        );
    }
}

if (!function_exists('twillModel')) {
    function twillModel($model)
    {
        return config(sprintf('twill.models.%s', $model))
            ?? abort(500, sprintf('helpers/twillModel: \'%s\' model is not configured', $model));
    }
}

if (!function_exists('generate_list_of_allowed_blocks')) {
    function generate_list_of_available_blocks(?array $blocks, ?array $groups): array
    {
        $blockList = TwillBlocks::getBlocks();

        $appBlocksList = $blockList->filter(function (Block $block) {
            return $block->source !== A17\Twill\Services\Blocks\Block::SOURCE_TWILL;
        });

        $finalBlockList = $blockList->filter(
            function (Block $block) use ($blocks, $groups, $appBlocksList) {
                if ($block->group === A17\Twill\Services\Blocks\Block::SOURCE_TWILL) {
                    if (! collect(config('twill.block_editor.use_twill_blocks'))->contains($block->name)) {
                        return false;
                    }

                    /** @var \Illuminate\Support\Collection<Block> $appBlocksList */
                    if (
                        count($appBlocksList) > 0 && $appBlocksList->contains(
                            function ($appBlock) use ($block) {
                                return $appBlock->name === $block->name;
                            }
                        )
                    ) {
                        return false;
                    }
                }

                return (filled($blocks) ? collect($blocks)->contains($block->name) : true)
                    && (filled($groups) ? collect($groups)->contains($block->group) : true);
            }
        );

        // Sort them by the original definition
        $test =  $finalBlockList->sortBy(function (Block $b) use ($blocks) {
            return collect($blocks)->search(function ($id, $key) use ($b) {
                return $id == $b->name;
            });
        })->values()->toArray();

        return $test;
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
