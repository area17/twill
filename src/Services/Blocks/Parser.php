<?php

namespace A17\Twill\Services\Blocks;

use A17\Twill\Commands\Behaviors\Blocks;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class Parser
{
    /**
     * @var \Illuminate\Support\Collection
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

        $this->generatePaths()->parseAllBlocks();
    }

    public function all()
    {
        return $this->blocks;
    }

    public function parseAllBlocks()
    {
        $this->blocks = collect($this->paths)->reduce(function ($keep, $path) {
            return $keep->merge($this->listBlocks($path['path'], $path['source'], $path['type']));
        }, collect());
    }

    public function generatePaths()
    {
        $this->paths = [
            ['path' => config_path('twill-utils/blocks'), 'source' => 'custom', 'type' => 'block'],
            ['path' => config_path('twill-utils/repeaters'), 'source' => 'custom', 'type' => 'repeater'],
            ['path' => __DIR__ . '/../../Commands/stubs/blocks', 'source' => 'twill', 'type' => 'block'],
            ['path' => __DIR__ . '/../../Commands/stubs/repeaters', 'source' => 'twill', 'type' => 'repeater'],
            ['path' => resource_path('views/admin/blocks'), 'source' => 'app', 'type' => null],
        ];

        return $this;
    }

    public function listBlocks($directory, $source, $type = null)
    {
        return collect($this->files->files($directory))->map(function ($file) use ($source, $type) {
            return $this->parseFile($file, $type)->merge(['source' => $source]);
        });
    }

    public function parseFile($file, $type = null)
    {
        $block = file_get_contents((string) $file);

        $blockName = Str::before($file->getFilename(),'.blade.php');

        [$title, $inferredType] = $this->getProperty('title', $block, $blockName);
        [$icon] = $this->getProperty('icon', $block, $blockName);
        [$trigger] = $this->getProperty('trigger', $block, $blockName);

        return collect([
            'title' => $title,
            'trigger' => $trigger,
            'name' => $blockName,
            'type' => $type ?? $inferredType,
            'icon' => $icon,
        ]);
    }

    public function getProperty($property, $block, $blockName)
    {
        preg_match("/@{$property}\(\'(.*)\'\)/", $block, $matches);

        if (filled($matches)) {
            return [$matches[1], 'block'];
        }

        if ($value = config("twill.block_editor.blocks.{$blockName}.{$property}")) {
            return [$value, 'block'];
        }

        if ($value = config("twill.block_editor.repeaters.{$blockName}.{$property}")) {
            return [$value, 'repeater'];
        }

        if ($property !== 'title') {
            return [null, null];
        }

        throw new \Exception("Property '{$property}' not found on block {$blockName}.");
    }
}
