<?php

namespace A17\Twill\Services\Blocks;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Parser
{
    /**
     * @var \A17\Twill\Services\Blocks\BlockCollection
     */
    protected $blocks;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $paths;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function all()
    {
        return $this->parse()->blocks;
    }

    public function parse()
    {
        $this->generatePaths();

        $this->blocks = (new BlockCollection($this->paths))->reduce(function (
            $keep,
            $path
        ) {
            $this->listBlocks(
                $path['path'],
                $path['source'],
                $path['type']
            )->each(function ($block) use ($keep) {
                $keep->push($block);

                return $keep;
            });

            return $keep;
        },
        new BlockCollection());

        $this->blocks = $this->blocks->each(function ($block) {
            $block->setSource($this->detectCustomSources($block));
        });

        return $this;
    }

    public function detectCustomSources($block)
    {
        if ($block->source === Block::SOURCE_APP) {
            if (
                $this->blocks
                    ->where('fileName', $block->fileName)
                    ->where('source', Block::SOURCE_TWILL)
                    ->isNotEmpty()
            ) {
                return Block::SOURCE_CUSTOM;
            }
        }

        return $block->source;
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

    public function listBlocks($directory, $source, $type = null)
    {
        if (!$this->files->exists($directory))
        {
            return collect();
        }

        return collect($this->files->files($directory))->map(function (
            $file
        ) use ($source, $type) {
            return $this->parseFile($file, $type)->setSource($source);
        });
    }

    public function parseFile($file, $type = null)
    {
        $contents = file_get_contents((string) $file);

        $name = Str::before($file->getFilename(), '.blade.php');

        [$title, $inferredType] = $this->getProperty('title', $contents, $name);
        [$icon] = $this->getProperty('icon', $contents, $name);
        [$trigger] = $this->getProperty('trigger', $contents, $name);

        return new Block([
            'title' => $title,
            'trigger' => $trigger,
            'name' => $name,
            'type' => $type ?? $inferredType,
            'icon' => $icon,
            'new_format' => $this->isUpgradedBlock($contents),
            'inferred_type' => $inferredType,
            'file' => $file,
            'contents' => $contents,
        ]);
    }

    public function getProperty($property, $block, $blockName)
    {
        preg_match("/@tw-{$property}\(\'(.*)\'\)/", $block, $matches);

        if (filled($matches)) {
            return [$matches[1], 'block'];
        }

        if (
            $value = config(
                "twill.block_editor.blocks.{$blockName}.{$property}"
            )
        ) {
            return [$value, 'block'];
        }

        if (
            $value = config(
                "twill.block_editor.repeaters.{$blockName}.{$property}"
            )
        ) {
            return [$value, 'repeater'];
        }

        if ($property !== 'title') {
            return [null, null];
        }

        throw new \Exception(
            "Property '{$property}' not found on block {$blockName}."
        );
    }

    public function isUpgradedBlock($block)
    {
        preg_match("/@tw-.*\(\'(.*)\'\)/", $block, $matches);

        return filled($matches);
    }

    public function getAllowedBlocksList()
    {
        return $this->all()->mapWithKeys(function ($block) {
            return $block->legacyArray();
        });
    }
}
