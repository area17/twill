<?php

namespace A17\Twill\Services\Blocks;

use A17\Twill\Facades\TwillBlocks;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BlockCollection extends Collection
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $paths;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $fileSystem;

    private \Illuminate\Support\Collection $missingDirectories;

    /**
     * @param mixed $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);

        $this->fileSystem = app(Filesystem::class);

        $this->missingDirectories = collect();

        $this->load();
    }

    private function addMissingDirectory($directory): void
    {
        $this->missingDirectories->push($directory);
    }

    /**
     * @param $search
     * @return mixed
     * @throws \Exception
     */
    public function findByName($search, array $sources = [])
    {
        return $this->collect()
            ->filter(function ($block) use ($search, $sources): bool {
                return $block->name == $search &&
                    (blank($sources) ||
                    collect($sources)->contains($block->source));
            })
            ->sortBy(function ($block): int {
                return $block->source === 'app' ? 0 : 1;
            })
            ->first();
    }

    public function getBlocks(): \Illuminate\Support\Collection
    {
        return $this->collect()
            ->filter(function ($block): bool {
                return $block->type === Block::TYPE_BLOCK;
            })
            ->values();
    }

    public function getBlockList(): \Illuminate\Support\Collection
    {
        return $this->getBlocks();
    }

    public function getMissingDirectories(): \Illuminate\Support\Collection
    {
        return $this->missingDirectories;
    }

    /**
     * @param $directory
     * @param $source
     * @param null $type
     * @return \Illuminate\Support\Collection
     *
     * @deprecated Removed in 3.x
     */
    public function readBlocks($directory, $source, $type = null)
    {
        if (! $this->fileSystem->exists($directory)) {
            $this->addMissingDirectory($directory);

            return collect();
        }

        return TwillBlocks::readBlocksFromDirectory($directory, $source, $type);
    }

    /**
     * @return $this
     */
    public function generatePaths(): static
    {
        $this->paths = collect(
            config('twill.block_editor.directories.source.blocks')
        )
            ->map(function ($path) {
                $path['type'] = Block::TYPE_BLOCK;

                return $path;
            })
            ->merge(
                collect(
                    config('twill.block_editor.directories.source.repeaters')
                )->map(function ($path) {
                    $path['type'] = Block::TYPE_REPEATER;

                    return $path;
                })
            );

        return $this;
    }

    public function detectCustomSources(Block $block): string
    {
        if ($block->source === Block::SOURCE_APP && $this->collect()
        ->where('fileName', $block->getFileName())
        ->where('source', Block::SOURCE_TWILL)
        ->isNotEmpty()) {
            return Block::SOURCE_CUSTOM;
        }

        return $block->source;
    }

    public function load(): self
    {
        $this->generatePaths();

        $this->items = collect($this->paths)
            ->reduce(function (Collection $keep, $path): \Illuminate\Support\Collection {
                $this->readBlocks(
                    $path['path'],
                    $path['source'],
                    $path['type']
                )->each(function ($block) use ($keep): \Illuminate\Support\Collection {
                    $keep->push($block);

                    return $keep;
                });

                return $keep;
            }, collect())
            ->toArray();

        $this->items = $this->collect()
            ->each(function (Block $block): void {
                $block->setSource($this->detectCustomSources($block));
            })
            ->toArray();

        // remove duplicate Twill blocks
        $appBlocks = $this->collect()->whereIn('source', [Block::SOURCE_APP, Block::SOURCE_CUSTOM]);
        $this->items = $this->collect()->filter(function ($item) use ($appBlocks): bool {
            return ! $appBlocks->contains(function ($block) use ($item): bool {
                return $item->source === Block::SOURCE_TWILL && $item->name === $block->name;
            });
        })->values()->toArray();

        $this
            ->addBlocksFromConfig(collect(config('twill.block_editor.repeaters')), Block::TYPE_REPEATER)
            ->addBlocksFromConfig(collect(config('twill.block_editor.blocks')), Block::TYPE_BLOCK);

        return $this;
    }

    /**
     * This function will add blocks and repeaters that are only defined in the config.
     *
     * For compatibility with 2.0.2 and lower
     */
    public function addBlocksFromConfig(Collection $items, string $type): self
    {
        $items->reject(function ($value, $blockName) use ($type): array|bool {
            return $this->contains(function ($block) use ($blockName, $type): bool {
                return $block->name === $blockName && $block->type === $type;
            }) ? [$blockName, $value] : false;
        })
            ->each(function ($block, $blockName) use ($type): void {
                $file = $block['compiled'] ?? false ? null : $this->findFileByComponentName($block['component']);

                $this->push($this->blockFromComponentName(
                    $file,
                    $blockName,
                    $type,
                    Block::SOURCE_APP
                ));
            });

        return $this;
    }

    /**
     * @param string $componentName
     */
    public function blockFromComponentName($file, string $blockName, string $type, string $source): \A17\Twill\Services\Blocks\Block
    {
        $this->logDeprecatedBlockConfig($blockName, $type);

        return Block::make($file, $type, $source, $blockName);
    }

    public function logDeprecatedBlockConfig(string $blockName, string $type): void
    {
        $path = $this->paths->filter(function ($path) use ($type): bool {
            return $path['source'] === Block::SOURCE_APP && $path['type'] === $type;
        })->pluck('path')->join(', ', ' or ');

        Log::notice(
            sprintf('The %s \'%s\' appears to be defined in the config ', $type, $blockName) .
            "'twill.block_editor.blocks' or 'twill.block_editor.repeaters' only. " .
            sprintf('This will be deprecated in a future release. A %s should be ', $type) .
            sprintf('defined in its unique view in [%s].', $path)
        );
    }

    /**
     * This function will try to find a view from the a component name
     * (minus the 'a17-block-' namespace).
     */
    public function findFileByComponentName(string $componentName): \Symfony\Component\Finder\SplFileInfo
    {
        $filename = str_replace('a17-block-', '', $componentName) . '.blade.php';
        $paths = $this->paths->pluck('path')->filter(function ($path): bool {
            return $this->fileSystem->exists($path);
        })->toArray();

        $files = iterator_to_array(\Symfony\Component\Finder\Finder::create()->name($filename)->in($paths), false);

        if (empty($files)) {
            throw new Exception(sprintf('Could not find a view for the block or repeater \'%s\'.', $componentName));
        }

        return $files[0];
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return $this->list()->toArray();
    }

    public function list(): \Illuminate\Support\Collection
    {
        return $this->collect()->map(function (Block $block): \Illuminate\Support\Collection {
            return $block->toList();
        });
    }

    public function collect(): \Illuminate\Support\Collection
    {
        return collect($this);
    }

    public function getRepeaters(): \Illuminate\Support\Collection
    {
        return $this->collect()
            ->filter(function ($block): bool {
                return $block->type === Block::TYPE_REPEATER;
            })
            ->values();
    }

    public function getRepeaterList(): \Illuminate\Support\Collection
    {
        return $this->getRepeaters();
    }
}
