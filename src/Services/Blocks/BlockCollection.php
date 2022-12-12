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

    /**
     * @var \Illuminate\Support\Collection
     */
    private $missingDirectories;

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

    private function addMissingDirectory($directory)
    {
        $this->missingDirectories->push($directory);
    }

    /**
     * @param $search
     * @param array $sources
     * @return mixed
     * @throws \Exception
     */
    public function findByName($search, $sources = [])
    {
        return $this->collect()
            ->filter(function ($block) use ($search, $sources) {
                return $block->name == $search &&
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

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getBlocks()
    {
        return $this->collect()
            ->filter(function ($block) {
                return $block->type === Block::TYPE_BLOCK;
            })
            ->values();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getBlockList()
    {
        return $this->getBlocks();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getMissingDirectories()
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
    public function generatePaths()
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

    /**
     * @return string
     */
    public function detectCustomSources(Block $block)
    {
        if (
            $block->source === Block::SOURCE_APP && $this->collect()
                ->where('fileName', $block->getFileName())
                ->where('source', Block::SOURCE_TWILL)
                ->isNotEmpty()
        ) {
            return Block::SOURCE_CUSTOM;
        }

        return $block->source;
    }

    public function load(): self
    {
        $this->generatePaths();

        $this->items = collect($this->paths)
            ->reduce(function (Collection $keep, $path) {
                $this->readBlocks(
                    $path['path'],
                    $path['source'],
                    $path['type']
                )->each(function ($block) use ($keep) {
                    $keep->push($block);

                    return $keep;
                });

                return $keep;
            }, collect())
            ->toArray();

        $this->items = $this->collect()
            ->each(function (Block $block) {
                $block->setSource($this->detectCustomSources($block));
            })
            ->toArray();

        // remove duplicate Twill blocks
        $appBlocks = $this->collect()->whereIn('source', [Block::SOURCE_APP, Block::SOURCE_CUSTOM]);
        $this->items = $this->collect()->filter(function ($item) use ($appBlocks) {
            return ! $appBlocks->contains(function ($block) use ($item) {
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

    /**
     * @param string $componentName
     * @param string $blockName
     * @param string $type
     * @param string $source
     * @return Block
     */
    public function blockFromComponentName($file, $blockName, $type, $source)
    {
        $this->logDeprecatedBlockConfig($blockName, $type);

        return Block::make($file, $type, $source, $blockName);
    }

    /**
     * @param string $type
     * @param string $blockName
     * @return void
     */
    public function logDeprecatedBlockConfig($blockName, $type)
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
     *
     * @param string $componentName
     * @return \Symfony\Component\Finder\SplFileInfo
     */
    public function findFileByComponentName($componentName)
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

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->list()->toArray();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function list()
    {
        return $this->collect()->map(function (Block $block) {
            return $block->toList();
        });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collect()
    {
        return collect($this);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRepeaters()
    {
        return $this->collect()
            ->filter(function ($block) {
                return $block->type === Block::TYPE_REPEATER;
            })
            ->values();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRepeaterList()
    {
        return $this->getRepeaters();
    }
}
