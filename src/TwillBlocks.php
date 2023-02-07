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

        foreach (self::$manualBlocks as $class) {
            $this->blockCollection->add(
                Block::forComponent($class)
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

    public function registerManualBlock(string $blockClass): void
    {
        self::$manualBlocks[$blockClass] = $blockClass;
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
}
