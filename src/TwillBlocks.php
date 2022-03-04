<?php

namespace A17\Twill;

use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Blocks\BlockCollection;
use Illuminate\Support\Collection;

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

    public function registerPackageBlocksDirectory(string $path): void
    {
        self::$blockDirectories[$path] = self::DIRECTORY_TYPE_VENDOR;
    }

    public function registerPackageRepeatersDirectory(string $path): void
    {
        self::$repeatersDirectories[$path] = self::DIRECTORY_TYPE_VENDOR;
    }

    public function getBlockCollection(): BlockCollection
    {
        return new BlockCollection();
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
}
