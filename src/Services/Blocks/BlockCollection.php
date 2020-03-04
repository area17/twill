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
     * @var Filesystem
     */
    protected $files;

    /**
     * @param mixed $items
     * @param Filesystem $files
     */
    public function __construct($items = [], Filesystem $files = null)
    {
        parent::__construct($items);

        $this->files = $files;
    }

    public function findByName($search, $sources = [])
    {
        $block = new Block();

        return (new static($this->items))
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

    public function getAllowedBlocksList()
    {
        return $this->all()->mapWithKeys(function ($block) {
            return $block->legacyArray();
        });
    }

    public function listBlocks($directory, $source, $type = null)
    {
        if (!$this->files->exists($directory)) {
            return collect();
        }

        return collect($this->files->files($directory))->map(function (
            $file
        ) use ($source, $type) {
            return new Block($file, $type, $source);
        });
    }

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

    public function detectCustomSources($block)
    {
        if ($block->source === Block::SOURCE_APP) {
            if (
                $this->all()
                    ->where('fileName', $block->getFileName())
                    ->where('source', Block::SOURCE_TWILL)
                    ->isNotEmpty()
            ) {
                return Block::SOURCE_CUSTOM;
            }
        }

        return $block->source;
    }

    public function parse()
    {
        $this->generatePaths();

        $this->items = collect($this->paths)->reduce(function ($keep, $path) {
            $this->listBlocks(
                $path['path'],
                $path['source'],
                $path['type']
            )->each(function ($block) use ($keep) {
                $keep->push($block);

                return $keep;
            });

            return $keep;
        }, collect());

        $this->items = $this->items
            ->each(function ($block) {
                $block->setSource($this->detectCustomSources($block));
            })
            ->toArray();

        return $this;
    }
}
