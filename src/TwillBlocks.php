<?php

namespace A17\Twill;

use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Blocks\BlockCollection;
use A17\Twill\Services\Forms\InlineRepeater;
use A17\Twill\View\Components\Blocks\TwillBlockComponent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TwillBlocks
{
    /**
     * @var array<string, array>
     */
    public static $blockDirectories = [];

    /**
     * @var array<string, array>
     */
    public static $repeatersDirectories = [];

    /**
     * @var array<string, string>
     */
    public static $componentBlockNamespaces = [];

    /**
     * @var array<string, InlineRepeater>
     */
    public static $dynamicRepeaters = [];

    /**
     * @var array
     */
    public static $loadedDynamicRepeaters = [];

    /**
     * @var array<string, string>
     */
    public static $manualBlocks = [];

    /**
     * @return A17\Twill\Services\Blocks\BlockCollection
     */
    private ?BlockCollection $blockCollection = null;

    private array $cropConfigs = [];

    /**
     * Registers a blocks directory.
     *
     * When the blockCollection is already initialized, we read the blocks and merge them in.
     * If the blockCollection is not yet initialized, we add it to the local static so that we
     * can process it once the collection is needed.
     */
    public function registerPackageBlocksDirectory(string $path, string $renderNamespace = null): void
    {
        if (! isset(self::$blockDirectories[$path])) {
            if (isset($this->blockCollection)) {
                $this->getBlockCollection()->merge(
                    $this->readBlocksFromDirectory($path, Block::SOURCE_VENDOR, Block::TYPE_BLOCK)
                );
            } else {
                self::$blockDirectories[$path] = [
                    'source' => Block::SOURCE_VENDOR,
                    'renderNamespace' => $renderNamespace,
                ];
            }
        }
    }

    public function registerDynamicRepeater(string $name, InlineRepeater $repeater): void
    {
        self::$dynamicRepeaters[$name] = $repeater;
    }

    public function discoverDynamicRepeaters(Collection $collection): void
    {
        /** @var Block $item */
        foreach ($collection as $item) {
            if ($item->componentClass) {
                $component = new $item->componentClass();
                $component->getForm()->registerDynamicRepeaters();
            }
        }
    }

    public function getAvailableRepeaters(): string
    {
        $baseList = $this->getBlockCollection()->getRepeaters()->mapWithKeys(function (Block $repeater) {
            return [$repeater->name => $repeater->toList()];
        });

        return $baseList->toJson();
    }

    public function registerComponentBlocks(string $namespace, string $path): void
    {
        if (! Str::startsWith($namespace, '\\')) {
            $namespace = '\\' . $namespace;
        }

        self::$componentBlockNamespaces[$namespace] = $path;
    }

    /**
     * Registers a repeaters directory.
     *
     * When the blockCollection is already initialized, we read the repeaters and merge them in.
     * If the blockCollection is not yet initialized, we add it to the local static so that we
     * can process it once the collection is needed.
     */
    public function registerPackageRepeatersDirectory(string $path, string $renderNamespace = null): void
    {
        if (! isset(self::$repeatersDirectories[$path])) {
            if (isset($this->blockCollection)) {
                $this->getBlockCollection()->merge(
                    $this->readBlocksFromDirectory($path, Block::SOURCE_VENDOR, Block::TYPE_REPEATER)
                );
            } else {
                self::$repeatersDirectories[$path] = [
                    'source' => Block::SOURCE_VENDOR,
                    'renderNamespace' => $renderNamespace,
                ];
            }
        }
    }


    protected array $globallyExcludedBlocks = [];

    /** Util method to be called in a service provider to prevent some of a package's block to be opt in */
    public function globallyExcludeBlocks(array|callable $blocks): void
    {
        $this->globallyExcludedBlocks[] = $blocks;
    }

    public function getGloballyExcludedBlocks(): array
    {
        return $this->globallyExcludedBlocks;
    }

    /**
     * Only when the block collection is actually requested we parse all the information.
     */
    public function getBlockCollection(): BlockCollection
    {
        if (! isset($this->blockCollection)) {
            $this->blockCollection = new BlockCollection();
        }

        // Consume the repeatersDirectories. We act a bit dumb here by not taking into account duplicates
        // as a package should only register a directory once.
        foreach (self::$repeatersDirectories as $repeaterDir => $data) {
            foreach (
                $this->readBlocksFromDirectory(
                    $repeaterDir,
                    $data['source'],
                    Block::TYPE_REPEATER,
                    $data['renderNamespace']
                ) as $repeater
            ) {
                $this->blockCollection->add($repeater);
            }

            unset(self::$repeatersDirectories[$repeaterDir]);
        }

        foreach (self::$blockDirectories as $blockDir => $data) {
            foreach (
                $this->readBlocksFromDirectory(
                    $blockDir,
                    $data['source'],
                    Block::TYPE_BLOCK,
                    $data['renderNamespace']
                ) as $block
            ) {
                $this->blockCollection->add($block);
            }

            unset(self::$blockDirectories[$blockDir]);
        }


        foreach (self::$componentBlockNamespaces as $namespace => $path) {
            if (file_exists($path)) {
                $disk = Storage::build([
                    'driver' => 'local',
                    'root' => $path,
                ]);

                foreach ($disk->allFiles() as $file) {
                    $class = $namespace . '\\' . Str::replace('/', '\\', Str::before($file, '.'));
                    if (is_subclass_of($class, TwillBlockComponent::class)) {
                        $this->blockCollection->add(
                            Block::forComponent($class)
                        );
                    }
                }
            }

            unset(self::$componentBlockNamespaces[$namespace]);
        }

        foreach (self::$manualBlocks as $class => $source) {
            $this->blockCollection->add(
                Block::forComponent($class, $source)
            );

            unset(self::$manualBlocks[$class]);
        }

        $this->discoverDynamicRepeaters($this->blockCollection);

        foreach (self::$dynamicRepeaters as $name => $dynamicRepeater) {
            if (! isset(self::$loadedDynamicRepeaters[$name])) {
                $this->blockCollection->add($dynamicRepeater->asBlock());
                self::$loadedDynamicRepeaters[$name] = true;
            }
        }

        // remove duplicate Twill blocks
        $appBlocks = $this->blockCollection->where('source', '!=', Block::SOURCE_TWILL);
        $this->blockCollection = $this->blockCollection->filter(function ($item) use ($appBlocks) {
            return ! $appBlocks->contains(function ($block) use ($item) {
                return $item->source === Block::SOURCE_TWILL && $item->name === $block->name;
            });
        });

        return $this->blockCollection;
    }

    public function registerManualBlock(string $blockClass, string $source = Block::SOURCE_APP): void
    {
        self::$manualBlocks[$blockClass] = $source;
    }

    public function findByName(string $name): ?Block
    {
        return $this->getAll()->first(function (Block $block) use ($name) {
            return $block->name === $name;
        });
    }

    public function findRepeaterByName(string $name): ?Block
    {
        $repeater = $this->getRepeaters()->first(function (Block $block) use ($name) {
            return $block->name === $name;
        });

        if ($repeater === null) {
            // Search for the dynamic one.
            $repeater = $this->getRepeaters()->first(function (Block $block) use ($name) {
                return $block->name === 'dynamic-repeater-' . $name;
            });
        }

        return $repeater;
    }

    /**
     * Gets all blocks and repeaters.
     *
     * @return Collection|Block[]
     */
    public function getAll(): Collection
    {
        return $this->getBlocks()->merge($this->getRepeaters())->merge($this->getSettingsBlocks());
    }

    /**
     * @return Collection|Block[]
     */
    public function getBlocks(bool $withSettingsBlocks = false): Collection
    {
        $blocks = $this->getBlockCollection()->getBlockList();

        if ($withSettingsBlocks) {
            return $blocks->merge($this->getSettingsBlocks());
        }

        return $blocks;
    }

    public function getSettingsBlocks(): Collection
    {
        return $this->getBlockCollection()->getSettingsList();
    }

    /**
     * @return Collection|Block[]
     */
    public function getRepeaters(): Collection
    {
        return $this->getBlockCollection()->getRepeaters();
    }

    /**
     * Gets the collection of Block objects from a given directory.
     */
    public function readBlocksFromDirectory(
        string $directory,
        string $source,
        string $type,
        ?string $renderNamespace = null
    ): Collection {
        if (! File::exists($directory)) {
            return new Collection();
        }

        return collect(File::files($directory))
            ->map(function ($file) use ($source, $type, $renderNamespace) {
                return Block::make($file, $type, $source, null, $renderNamespace);
            });
    }

    /**
     * Gets all the crop configs, also those of component blocks.
     */
    public function getAllCropConfigs(): array
    {
        if (! $this->cropConfigs) {
            $this->cropConfigs = config()->get('twill.block_editor.crops');

            /** @var Block $block */
            foreach ($this->getBlockCollection() as $block) {
                if (! $block->componentClass) {
                    continue;
                }

                $crops = $block->componentClass::getCrops();
                if ($crops !== []) {
                    $this->cropConfigs = array_merge($this->cropConfigs, $crops);
                }
            }
        }

        return $this->cropConfigs;
    }

    public function generateListOfAllBlocks(bool $settingsOnly = false)
    {
        return once(function () use ($settingsOnly) {
            /** @var Collection $blockList */
            if ($settingsOnly) {
                $blockList = TwillBlocks::getSettingsBlocks();
            } else {
                $blockList = TwillBlocks::getBlocks();
            }

            $appBlocksList = $blockList->filter(function (Block $block) {
                return $block->source !== Block::SOURCE_TWILL;
            });

            return $blockList->filter(function (Block $block) use ($appBlocksList) {
                if ($block->group === Block::SOURCE_TWILL) {
                    if (!collect(config('twill.block_editor.use_twill_blocks'))->contains($block->name)) {
                        return false;
                    }

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
                return true;
            })->sortBy(function (Block $b) {
                // Blocks are by default sorted by the order they have been found in directories, but we can allow individual blocks to override this behavior
                return $b->getPosition();
            })->values();
        });
    }

    public function generateListOfAvailableBlocks(?array $blocks = null, ?array $groups = null, bool $settingsOnly = false, array|callable $excludeBlocks = []): Collection
    {
        $globalExcludeBlocks = TwillBlocks::getGloballyExcludedBlocks();

        $matchBlock = function ($matcher, $block, $someFn = null) {
            if (is_callable($matcher)) {
                return call_user_func($matcher, $block);
            } elseif (!empty($matcher) && is_array($matcher)) {
                $class = ltrim($block->componentClass, '\\');
                return collect($matcher)->some($someFn ?: fn($ex) => $ex == $block->name || $ex == $class);
            }
            return null;
        };
        return $this->generateListOfAllBlocks($settingsOnly)->filter(
            function (Block $block) use ($blocks, $groups, $excludeBlocks, $globalExcludeBlocks, $matchBlock) {
                if ($matchBlock($excludeBlocks, $block)) {
                    return false;
                }

                // Allow list of blocks and groups should have priority over globally excluded blocks (or there would be no way of allowing them)
                if ($matchedBlock = $matchBlock($blocks, $block)) {
                    return true;
                }
                if ($matchedGroup = $matchBlock($groups, $block, fn($ex) => $ex === $block->group)) {
                    return true;
                }

                if ($matchedBlock === false || $matchedGroup === false) {
                    return false;
                }

                foreach ($globalExcludeBlocks as $excludeBlock) {
                    if ($matchBlock($excludeBlock, $block)) {
                        return false;
                    }
                }

                return true;
            }
        );
    }
}
