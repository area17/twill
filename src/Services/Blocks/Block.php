<?php

namespace A17\Twill\Services\Blocks;

use A17\Twill\Helpers\TwillBlock;
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
    public $titleField;

    /**
     * @var boolean
     */
    public $hideTitlePrefix;

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
     * @var string
     */
    private $rules = [];

    /**
     * @var string
     */
    private $rulesForTranslatedFields = [];

    /**
     * @var TwillBlock
     */
    private $helper;

    /**
     * Block constructor.
     * @param $file
     * @param $type
     * @param $source
     * @param $name
     * @throws \Exception
     */
    public function __construct($file, $type, $source, $name = null)
    {
        $this->file = $file;

        $this->type = $type;

        $this->source = $source;

        $this->fileName = $this->getFilename();

        $this->name = $name ?? Str::before(
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
            'titleField' => $this->titleField,
            'hideTitlePrefix' => $this->hideTitlePrefix,
            'trigger' => $this->trigger,
            'name' => $this->name,
            'group' => $this->group,
            'type' => $this->type,
            'icon' => $this->icon,
            'compiled' => $this->compiled ? 'yes' : '-',
            'source' => $this->source,
            'new_format' => $this->isNewFormat ? 'yes' : '-',
            'file' => $this->getFilename(),
            'component' => $this->component,
            'rules' => $this->getRules(),
            'rulesForTranslatedFields' => $this->getRulesForTranslatedFields(),
            'helper' => $this->helper(),
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
        $contents = $this->file ? file_get_contents((string) $this->file->getPathName()) : '';

        $this->title = $this->parseProperty('title', $contents, $this->name);
        $this->trigger = $this->parseProperty('trigger', $contents, $this->name, $this->type === self::TYPE_REPEATER ? twillTrans('twill::lang.fields.block-editor.add-item') : null);
        $this->max = (int) $this->parseProperty('max', $contents, $this->name, 999);
        $this->group = $this->parseProperty('group', $contents, $this->name, 'app');
        $this->icon = $this->parseProperty('icon', $contents, $this->name, 'text');
        $this->compiled = (boolean) $this->parseProperty('compiled', $contents, $this->name, false);
        $this->component = $this->parseProperty('component', $contents, $this->name, "a17-block-{$this->name}");
        $this->isNewFormat = $this->isNewFormat($contents);
        $this->contents = $contents;

        $this->parseArrayProperty('ValidationRules', $contents, $this->name, function ($value) {
            $this->rules = $value ?? [];
        });

        $this->parseArrayProperty('ValidationRulesForTranslatedFields', $contents, $this->name, function ($value) {
            $this->rulesForTranslatedFields = $value ?? [];
        });

        $this->parseMixedProperty('titleField', $contents, $this->name, function ($value, $options) {
            $this->titleField = $value;
            $this->hideTitlePrefix = (boolean) ($options['hidePrefix'] ?? false);
        });

        return $this;
    }

    public function helper(): ?TwillBlock {
        if (!$this->helper) {
            $this->helper = TwillBlock::getBlockClassForName($this->name);
        }
        return $this->helper;
    }

    /**
     * Checks both the blade file or helper class for validation rules. Returns in order the first one with data.
     */
    public function getRules(): array {
        if (!empty($this->rules)) {
            return $this->rules;
        }

        if ($this->helper) {
            return $this->helper->getRules();
        }

        return [];
    }

    /**
     * Checks both the blade file or helper class for validation rules. Returns in order the first one with data.
     */
    public function getRulesForTranslatedFields(): array {
        if (!empty($this->rulesForTranslatedFields)) {
            return $this->rulesForTranslatedFields;
        }

        if ($this->helper) {
            return $this->helper->getRulesForTranslatedFields();
        }

        return [];
    }

    /**
     * Parse a string property directive in the form of `@twillTypeProperty('value')`.
     *
     * @param string $property
     * @param string $block
     * @param string $blockName
     * @param string|null $default
     * @return string
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
        }

        return $this->parsePropertyFallback($property, $blockName, $default);
    }

    /**
     * Parse an array property directive in the form of `@twillTypeProperty([...])`
     * and pass the result to a given callback.
     *
     * @param string $property
     * @param string $block
     * @param string $blockName
     * @param Callable $callback  Should have the following signature: `function (array $value)`
     * @return void
     * @throws \Exception
     */
    public function parseArrayProperty(
        $property,
        $block,
        $blockName,
        $callback
    ): void {
        $this->parseMixedProperty($property, $block, $blockName, function($value) use ($callback) {
            $callback($value);
        });
    }

    /**
     * Parse a mixed property directive in the form of `@twillTypeProperty('value', [...])`
     * and pass the result to a given callback.
     *
     * @param string $property
     * @param string $block
     * @param string $blockName
     * @param Callable $callback  Should have the following signature: `function ($value, $options)`
     * @return mixed
     * @throws \Exception
     */
    public function parseMixedProperty(
        $property,
        $block,
        $blockName,
        $callback
    ) {
        $bladeProperty = ucfirst($property);

        foreach (['twillProp', 'twillBlock', 'twillRepeater'] as $pattern) {
            // Regexp modifiers:
            //   `s`  allows newlines as part of the `.*` match
            //   `U`  stops the match at the first closing parenthesis
            preg_match("/@{$pattern}{$bladeProperty}\((.*)\)/sU", $block, $matches);

            if (filled($matches)) {
                // Wrap the match in array notation and feed it to `eval` to get an actual array.
                // In this context, we're only interested in the first two possible values.
                $content = "[{$matches[1]}]";
                $parsedContent = eval("return {$content};");
                $value = $parsedContent[0] ?? null;
                $options = $parsedContent[1] ?? null;

                return $callback($value, $options);
            }
        };

        $value = $this->parseProperty($property, $block, $blockName, null);

        return $callback($value, null);
    }

    /**
     * @param $property
     * @param $blockName
     * @param null $default
     * @return mixed
     * @throws \Exception
     */
    private function parsePropertyFallback(
        $property,
        $blockName,
        $default = null
    ) {
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

        if ($configBlock = collect(config("twill.block_editor.blocks"))->filter(function ($block) use ($blockName) {
            return Str::contains($block['component'], $blockName);
        })->first()) {
            if ($value = ($configBlock[$property] ?? null)) {
                return $value;
            }
        }
        if ($configRepeater = collect(config("twill.block_editor.repeaters"))->filter(function ($repeater) use ($blockName) {
            return Str::contains($repeater['component'], $blockName);
        })->first()) {
            if ($value = ($configRepeater[$property] ?? null)) {
                return $value;
            }
        }

        if ($property !== 'title') {
            return $default;
        }

        // Title is mandatory
        throw new Exception(
            "Block {$blockName} does not exists or the mandatory property '{$property}' " .
            "was not found on this block. If you are still using blocks on the twill.php " .
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
        return $this->file ? $this->file->getFileName() : 'Custom Vue file';
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function render()
    {
        return BladeCompiler::render(
            self::removeSpecialBladeTags($this->contents),
            [
                'renderForBlocks' => true,
            ]
        );
    }

    /**
     * @param $contents
     * @return string
     */
    public static function removeSpecialBladeTags($contents)
    {
        return preg_replace([
            "/@twillProp.*\((.*)\)/sU",
            "/@twillBlock.*\((.*)\)/sU",
            "/@twillRepeater.*\((.*)\)/sU",
        ], '', $contents);
    }
}
