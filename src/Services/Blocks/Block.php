<?php

namespace A17\Twill\Services\Blocks;

use Illuminate\Support\Str;

class Block
{
    const SOURCE_APP = 'app';

    const SOURCE_TWILL = 'twill';

    const SOURCE_CUSTOM = 'custom';

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $trigger;

    /**
     * @var string
     */
    public $source;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $icon;

    /**
     * @var boolean
     */
    public $isNewFormat;

    /**
     * @var string
     */
    public $inferredType;

    /**
     * @var \Symfony\Component\Finder\SplFileInfo
     */
    public $file;

    /**
     * @var string
     */
    public $contents;

    public function __construct($file, $type, $source)
    {
        $this->file = $file;

        $this->type = $type;

        $this->source = $source;

        $this->parse();
    }

    public function absorbData($data)
    {
        if (blank($data)) {
            return;
        }

        $this->title = $data['title'];
        $this->trigger = $data['trigger'];
        $this->name = $data['name'];
        $this->type = $data['type'];
        $this->icon = $data['icon'];
        $this->isNewFormat = $data['new_format'];
        $this->inferredType = $data['inferred_type'];
        $this->contents = $data['contents'];

        return $this;
    }

    /**
     * @param string $path
     * @return Block
     */
    public function setPath(string $path): Block
    {
        $this->path = $path;

        $this->parse();

        return $this;
    }

    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    public function list()
    {
        return collect([
            'title' => $this->title,
            'trigger' => $this->trigger,
            'name' => $this->name,
            'type' => $this->type,
            'icon' => $this->icon,
            'source' => $this->source,
            'new_format' => $this->isNewFormat ? 'yes' : '-',
            'file' => $this->file->getFilename(),
        ]);
    }

    public function makeName($name)
    {
        return Str::kebab($name);
    }

    public function legacyArray()
    {
        return [
            $this->name => [
                'title' => $this->title,
                'icon' => $this->icon,
                'component' => 'a17-block-' . $this->name,
            ],
        ];
    }

    public function parse()
    {
        $contents = file_get_contents((string) $this->file->getPathName());

        $name = Str::before($this->file->getFilename(), '.blade.php');

        [$title, $inferredType] = $this->parseProperty(
            'title',
            $contents,
            $name
        );

        [$icon] = $this->parseProperty('icon', $contents, $name);

        [$trigger] = $this->parseProperty('trigger', $contents, $name);

        return $this->absorbData([
            'title' => $title,
            'trigger' => $trigger,
            'name' => $name,
            'type' => $type ?? $inferredType,
            'icon' => $icon,
            'new_format' => $this->isUpgradedBlock($contents),
            'inferred_type' => $inferredType,
            'contents' => $contents,
        ]);
    }

    public function parseProperty($property, $block, $blockName)
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

    public function getFileName()
    {
        return $this->file->getFileName();
    }
}
