<?php

namespace A17\Twill\Services\Blocks;

use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;

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
     * @param mixed $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);

        $this->fileSystem = app(Filesystem::class);

        $this->parse();
    }

    /**
     * @param $search
     * @param array $sources
     * @return mixed
     * @throws \Exception
     */
    public function findByName($search, $sources = [])
    {
        return $this->collection()
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

    /**
     * @return \A17\Twill\Services\Blocks\BlockCollection
     */
    public function getAllowedBlocksList()
    {
        return $this->mapWithKeys(function (Block $block) {
            return $block->legacyArray();
        });
    }

    /**
     * @param $directory
     * @param $source
     * @param null $type
     * @return \Illuminate\Support\Collection
     */
    public function listBlocks($directory, $source, $type = null)
    {
        if (!$this->fileSystem->exists($directory)) {
            return collect();
        }

        return collect($this->fileSystem->files($directory))->map(function (
            $file
        ) use ($source, $type) {
            return new Block($file, $type, $source);
        });
    }

    /**
     * @return $this
     */
    public function generatePaths()
    {
        $this->paths = [
            [
                'path' => __DIR__ . '/../../Commands/stubs/blocks',
                'source' => Block::SOURCE_TWILL,
                'type' => 'block',
            ],
            [
                'path' => __DIR__ . '/../../Commands/stubs/repeaters',
                'source' => Block::SOURCE_TWILL,
                'type' => 'repeater',
            ],
            [
                'path' => resource_path('views/admin/blocks'),
                'source' => Block::SOURCE_APP,
                'type' => null,
            ],
            [
                'path' => resource_path('views/admin/repeaters'),
                'source' => Block::SOURCE_APP,
                'type' => 'repeater',
            ],
        ];

        return $this;
    }

    /**
     * @param Block $block
     * @return string
     */
    public function detectCustomSources(Block $block)
    {
        if ($block->source === Block::SOURCE_APP) {
            if (
                $this->collection()
                    ->where('fileName', $block->getFileName())
                    ->where('source', Block::SOURCE_TWILL)
                    ->isNotEmpty()
            ) {
                return Block::SOURCE_CUSTOM;
            }
        }

        return $block->source;
    }

    /**
     * @return $this
     */
    public function parse()
    {
        $this->generatePaths();

        $this->items = collect($this->paths)
            ->reduce(function (Collection $keep, $path) {
                $this->listBlocks(
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

        $this->items = collect($this->items)
            ->each(function (Block $block) {
                $block->setSource($this->detectCustomSources($block));
            })
            ->toArray();

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return collect($this->items)
            ->map(function (Block $block) {
                return $block->export();
            })
            ->toArray();
    }

    public function collection()
    {
        return collect($this->items);
    }
}
