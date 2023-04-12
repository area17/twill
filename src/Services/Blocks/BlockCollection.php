<?php

namespace A17\Twill\Services\Blocks;

use Illuminate\Support\Collection;

class BlockCollection extends Collection
{
    protected Collection $paths;

    public function findByName(string $search, array $sources = []): ?Block
    {
        return $this->collect()
            ->filter(function ($block) use ($search, $sources) {
                return $block->name === $search &&
                    (blank($sources) ||
                        collect($sources)->contains($block->source));
            })
            ->sortBy(function ($block) {
                return $block->source === 'app' ? 0 : 1;
            })
            ->first();
    }

    public function getSettingsList(): Collection
    {
        return $this->collect()
            ->filter(function ($block) {
                return $block->type === Block::TYPE_SETTINGS;
            })
            ->values();
    }

    public function getBlocks(): Collection
    {
        return $this->collect()
            ->filter(function ($block) {
                return $block->type === Block::TYPE_BLOCK;
            })
            ->values();
    }

    public function getBlockList(): Collection
    {
        return $this->getBlocks();
    }

    public function toArray(): array
    {
        return $this->list()->toArray();
    }

    public function list(): Collection
    {
        return $this->collect()->map(function (Block $block) {
            return $block->toList();
        });
    }

    public function getRepeaters(): Collection
    {
        return $this->collect()
            ->filter(function ($block) {
                return $block->type === Block::TYPE_REPEATER;
            })
            ->values();
    }

    public function getRepeaterList(): Collection
    {
        return $this->getRepeaters();
    }
}
