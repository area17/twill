<?php

namespace A17\Twill\Services\Blocks;

use Exception;
use Illuminate\Support\Str;

class Block
{
    const SOURCE_APP = 'app';

    const SOURCE_TWILL = 'twill';

    const SOURCE_CUSTOM = 'custom';

    const TYPE_BLOCK = 'block';

    const TYPE_REPEATER = 'repeater';

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
    public $group;

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
    public $compiled;

    /**
     * @var string
     */
    public $component;

    /**
     * @var integer
     */
    public $max = 999;

    /**
     * @var boolean
     */
    public $isNewFormat;

    /**
     * @var \Symfony\Component\Finder\SplFileInfo
     */
    public $file;

    /**
     * @var string
     */
    public $fileName;

    /**
     * @var string
     */
    public $contents;

    /**
     * Block constructor.
     * @param $file
     * @param $type
     * @param $source
     * @throws \Exception
     */
    public function __construct($file, $type, $source)
    {
        $this->file = $file;

        $this->type = $type;

        $this->source = $source;

        $this->fileName = $this->getFilename();

        $this->name = $name = Str::before(
            $this->file->getFilename(),
            '.blade.php'
        );

        if ($type === self::TYPE_BLOCK
            && config('twill.block_editor.repeaters.' . $this->name) !== null
        ) {
            $this->type = self::TYPE_REPEATER;
        }

        $this->parse();
    }

    /**
     * @param $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function toList()
    {
        return collect([
            'title' => $this->title,
            'trigger' => $this->trigger,
            'name' => $this->name,
            'group' => $this->group,
            'type' => $this->type,
            'icon' => $this->icon,
            'compiled' => $this->compiled ? 'yes' : '-',
            'source' => $this->source,
            'new_format' => $this->isNewFormat ? 'yes' : '-',
            'file' => $this->file->getFilename(),
            'component' => $this->component,
            'max' => $this->type === self::TYPE_REPEATER ? $this->max : null,
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function toShortList()
    {
        return collect([
            'title' => $this->title,
            'name' => $this->name,
            'group' => $this->group,
            'type' => $this->type,
            'icon' => $this->icon,
        ]);
    }

    /**
     * @param $name
     * @return string
     */
    public function makeName($name)
    {
        return Str::kebab($name);
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function parse()
    {
        $contents = file_get_contents((string) $this->file->getPathName());

        $this->title = $this->parseProperty('title', $contents, $this->name);
        $this->trigger = $this->parseProperty('trigger', $contents, $this->name);
        $this->max = (int) $this->parseProperty('max', $contents, $this->name, 999);
        $this->group = $this->parseProperty('group', $contents, $this->name, 'app');
        $this->icon = $this->parseProperty('icon', $contents, $this->name);
        $this->compiled = (boolean) $this->parseProperty('compiled', $contents, $this->name, false);
        $this->component = $this->parseProperty('component', $contents, $this->name, "a17-block-{$this->name}");
        $this->isNewFormat = $this->isNewFormat($contents);
        $this->contents = $contents;

        return $this;
    }

    /**
     * @param $property
     * @param $block
     * @param $blockName
     * @param null $default
     * @return array
     * @throws \Exception
     */
    public function parseProperty(
        $property,
        $block,
        $blockName,
        $default = null
    ) {
        $bladeProperty = ucfirst($property);

        foreach (['twillProp', 'twillBlock', 'twillRepeater'] as $pattern) {
            preg_match("/@{$pattern}{$bladeProperty}\('(.*)'\)/", $block, $matches);

            if (filled($matches)) {
                return $matches[1];
            }
        };

        if (
            $value = config(
                "twill.block_editor.blocks.{$blockName}.{$property}"
            )
        ) {
            return $value;
        }

        if (
            $value = config(
                "twill.block_editor.repeaters.{$blockName}.{$property}"
            )
        ) {
            return $value;
        }

        if ($property !== 'title') {
            return $default;
        }

        // Title is mandatory
        throw new Exception(
            "Block {$blockName} does not exists or the mandatory property '{$property}' ".
            "was not found on this block. If you are still using blocks on the twill.php ".
            "file, please check if the block is present and properly configured."
        );
    }

    /**
     * @param $block
     * @return bool
     */
    public function isNewFormat($block)
    {
        preg_match("/@twill(Prop|Block|Repeater).*\('(.*)'\)/", $block, $propMatches);

        return filled($propMatches);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->file->getFileName();
    }

    /**
     * @return string
     * @throws \Symfony\Component\Debug\Exception\FatalThrowableError
     */
    public function render()
    {
        return BladeCompiler::render(
            $this->removeSpecialBladeTags($this->contents),
            [
                'renderForBlocks' => true,
            ]
        );
    }

    /**
     * @param $contents
     * @return string
     */
    public function removeSpecialBladeTags($contents)
    {
        return preg_replace([
            "/@twillProp.*\('(.*)'\)/",
            "/@twillBlock.*\('(.*)'\)/",
            "/@twillRepeater.*\('(.*)'\)/",
        ], '', $contents);
    }
}
