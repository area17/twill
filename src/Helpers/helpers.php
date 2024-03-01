<?php

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Facades\TwillCapsules;
use A17\Twill\Models\Model;
use A17\Twill\Services\Blocks\Block;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

if (! function_exists('dumpUsableSqlQuery')) {
    function dumpUsableSqlQuery(Builder $query): void
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
    function classUsesDeep(string|object $class, bool $autoload = true): array
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
    function classHasTrait(string|object $class, string $trait): bool
    {
        $traits = classUsesDeep($class);

        return array_key_exists($trait, $traits);
    }
}

if (! function_exists('getFormFieldsValue')) {
    function getFormFieldsValue(array|ArrayAccess $formFields, string $name, mixed $default = null): mixed
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
    function fireCmsEvent(string $eventName, array $input = []): void
    {
        $method = method_exists(Dispatcher::class, 'dispatch') ? 'dispatch' : 'fire';
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

        // If it still starts with App, use the left part, otherwise use the whole namespace
        // This can be a problem for those using a completely different app path for the application
        $left = ($namespaceParts[1] === 'App' ? $namespaceParts[2] : $namespaceParts[0]);

        // Join, fix slashes for the current operating system, and return path
        return app_path(fix_directory_separator(
            $left . (filled($path) ? '\\' . $path : '')
        ));
    }
}

if (! function_exists('make_twill_directory')) {
    function make_twill_directory(string $path, bool $recursive = true, Filesystem $fs = null): void
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
    function twill_put_stub(string $path, string $stub, Filesystem $fs = null): void
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
    function fix_directory_separator(string $path): string
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
    function generate_list_of_available_blocks(?array $blocks = null, ?array $groups = null, bool $settingsOnly = false, array|callable $excludeBlocks = []): array
    {
        if ($settingsOnly) {
            $blockList = TwillBlocks::getSettingsBlocks();
        } else {
            $blockList = TwillBlocks::getBlocks();
        }

        $appBlocksList = $blockList->filter(function (Block $block) {
            return $block->source !== A17\Twill\Services\Blocks\Block::SOURCE_TWILL;
        });

        $finalBlockList = $blockList->filter(
            function (Block $block) use ($blocks, $groups, $appBlocksList, $excludeBlocks) {
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

                if (in_array($block->name, $excludeBlocks)) {
                    return false;
                }

                return (filled($blocks) ? collect($blocks)->contains($block->name) || collect($blocks)->contains(ltrim($block->componentClass, '\\')) : true)
                    && (filled($groups) ? collect($groups)->contains($block->group) : true);
            }
        );

        return $finalBlockList->values()->all();
    }
}

if (! function_exists('capsule_namespace')) {
    /**
     * TODO remove in v4
     * @deprecated use TwillCapsules::capsuleNamespace instead
     */
    function capsule_namespace(string $capsuleName, string $type = null): string
    {
        trigger_deprecation('area17/twill', '3.3', __FUNCTION__ . ' is deprecated and will be removed in 4.x, use TwillCapsules::capsuleNamespace instead');

        return TwillCapsules::capsuleNamespace($capsuleName, $type);
    }
}

if (! function_exists('capsule_namespace_to_path')) {
    /**
     * TODO remove in v4
     * @deprecated use TwillCapsules::capsuleNamespaceToPath instead
     */
    function capsule_namespace_to_path(string $namespace, string $capsuleNamespace, string $rootPath): string
    {
        trigger_deprecation('area17/twill', '3.3', __FUNCTION__ . ' is deprecated and will be removed in 4.x, use TwillCapsules::capsuleNamespaceToPath instead');

        return TwillCapsules::capsuleNamespaceToPath($namespace, $capsuleNamespace, $rootPath);
    }
}

if (! function_exists('str_after_last')) {
    /**
     * TODO remove in v4
     * @deprecated use Str::afterLast instead
     */
    function str_after_last($subject, $search): string
    {
        trigger_deprecation('area17/twill', '3.3', __FUNCTION__ . ' is deprecated and will be removed in 4.x, use Str::afterLast instead');

        return Str::afterLast($subject, $search);
    }
}
