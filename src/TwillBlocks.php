<?php

namespace A17\Twill;

use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Blocks\BlockCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class TwillBlocks
{
    public const DIRECTORY_TYPE_VENDOR = 'vendor';

    /**
     * @var array<string, string>
     */
    public static $blockDirectories = [];

    /**
     * @var array<string, string>
     */
    public static $repeatersDirectories = [];

    /**
     * @return BlockCollection
     */
    private Collection $blockCollection;

    /**
     * Registers a blocks directory.
     *
     * When the blockCollection is already initialized, we read the blocks and merge them in.
     * If the blockCollection is not yet initialized, we add it to the local static so that we
     * can process it once the collection is needed.
     */
    public function registerPackageBlocksDirectory(string $path): void
    {
        if (! isset(self::$blockDirectories[$path])) {
            if (isset($this->blockCollection)) {
                $this->getBlockCollection()->merge(
                    $this->readBlocksFromDirectory($path, self::DIRECTORY_TYPE_VENDOR, Block::TYPE_BLOCK)
                );
            } else {
                self::$blockDirectories[$path] = self::DIRECTORY_TYPE_VENDOR;
            }
        }
    }

    /**
     * Registers a repeaters directory.
     *
     * When the blockCollection is already initialized, we read the repeaters and merge them in.
     * If the blockCollection is not yet initialized, we add it to the local static so that we
     * can process it once the collection is needed.
     */
    public function registerPackageRepeatersDirectory(string $path): void
    {
        if (! isset(self::$repeatersDirectories[$path])) {
            if (isset($this->blockCollection)) {
                $this->getBlockCollection()->merge(
                    $this->readBlocksFromDirectory($path, self::DIRECTORY_TYPE_VENDOR, Block::TYPE_REPEATER)
                );
            } else {
                self::$repeatersDirectories[$path] = self::DIRECTORY_TYPE_VENDOR;
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
        foreach (self::$repeatersDirectories as $repeaterDir => $type) {
            foreach ($this->readBlocksFromDirectory($repeaterDir, $type, Block::TYPE_REPEATER) as $repeater) {
                $this->blockCollection->add($repeater);
            }
            unset(self::$repeatersDirectories[$repeaterDir]);
        }
        foreach (self::$blockDirectories as $blockDir => $type) {
            foreach ($this->readBlocksFromDirectory($blockDir, $type, Block::TYPE_BLOCK) as $block) {
                $this->blockCollection->add($block);
            }
            unset(self::$blockDirectories[$blockDir]);
        }

        return $this->blockCollection;
    }

    public function findByName(string $name): ?Block
    {
        return $this->getAll()->first(function (Block $block) use ($name) {
            return $block->name === $name;
        });
    }

    public function findRepeaterByName(string $name): ?Block
    {
        return $this->getRepeaters()->first(function (Block $block) use ($name) {
            return $block->name === $name;
        });
    }

    /**
     * Gets all blocks and repeaters.
     *
     * @return Collection|Block[]
     */
    public function getAll(): Collection
    {
        return $this->getBlocks()->merge($this->getRepeaters());
    }

    /**
     * @return Collection|Block[]
     */
    public function getBlocks(): Collection
    {
        return $this->getBlockCollection()->getBlockList();
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
    public function readBlocksFromDirectory(string $directory, string $source, string $type): Collection
    {
        if (! File::exists($directory)) {
            return new Collection();
        }

        return collect(File::files($directory))
            ->map(function ($file) use ($source, $type) {
                return Block::make($file, $type, $source);
            });
    }
}
