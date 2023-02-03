<?php

namespace A17\Twill\Services\Blocks;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Finder\SplFileInfo;

class BlockCollection extends Collection
{
    protected Collection $paths;

    /**
     * @return Collection<Block>
     */
    public function findByName(string $search, array $sources = []): Collection
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

    /**
     * This function will add blocks and repeaters that are only defined in the config.
     *
     * For compatibility with 2.0.2 and lower
     */
    public function addBlocksFromConfig(Collection $items, string $type): self
    {
        $items->reject(function ($value, $blockName) use ($type) {
            return $this->contains(function ($block) use ($blockName, $type) {
                return $block->name === $blockName && $block->type === $type;
            }) ? [$blockName, $value] : false;
        })
            ->each(function ($block, $blockName) use ($type) {
                $file = $block['compiled'] ?? false ? null : $this->findFileByComponentName($block['component']);

                $this->push(
                    $this->blockFromComponentName(
                        $file,
                        $blockName,
                        $type,
                        Block::SOURCE_APP
                    )
                );
            });

        return $this;
    }

    public function blockFromComponentName(string $file, string $blockName, string $type, string $source): Block
    {
        $this->logDeprecatedBlockConfig($blockName, $type);

        return Block::make($file, $type, $source, $blockName);
    }

    public function logDeprecatedBlockConfig(string $blockName, string $type): void
    {
        $path = $this->paths->filter(function ($path) use ($type) {
            return $path['source'] === Block::SOURCE_APP && $path['type'] === $type;
        })->pluck('path')->join(', ', ' or ');

        Log::notice(
            "The {$type} '{$blockName}' appears to be defined in the config " .
            "'twill.block_editor.blocks' or 'twill.block_editor.repeaters' only. " .
            "This will be deprecated in a future release. A {$type} should be " .
            "defined in its unique view in [{$path}]."
        );
    }

    /**
     * This function will try to find a view from the a component name
     * (minus the 'a17-block-' namespace).
     */
    public function findFileByComponentName(string $componentName): SplFileInfo
    {
        $filename = str_replace('a17-block-', '', $componentName) . '.blade.php';
        $paths = $this->paths->pluck('path')->filter(function ($path) {
            return $this->fileSystem->exists($path);
        })->toArray();

        $files = iterator_to_array(\Symfony\Component\Finder\Finder::create()->name($filename)->in($paths), false);

        if (empty($files)) {
            throw new Exception("Could not find a view for the block or repeater '{$componentName}'.");
        }

        return $files[0];
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
