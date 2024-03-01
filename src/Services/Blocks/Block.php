<?php

namespace A17\Twill\Services\Blocks;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Services\Forms\InlineRepeater;
use A17\Twill\View\Components\Blocks\TwillBlockComponent;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Container\Container;

class Block
{
    public const SOURCE_APP = 'app';

    public const SOURCE_TWILL = 'twill';

    public const SOURCE_CUSTOM = 'custom';

    public const SOURCE_VENDOR = 'vendor';

    public const TYPE_BLOCK = 'block';

    public const TYPE_SETTINGS = 'settings';

    public const TYPE_REPEATER = 'repeater';

    public const PREG_REPLACE_INNER = '(\(((?>[^()]+)|(?-2))*\))';

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $titleField;

    /**
     * @var bool
     */
    public $hideTitlePrefix;

    /**
     * @var string
     */
    public $trigger;

    /**
     * For repeaters only: The select existing button text.
     */
    public ?string $selectTrigger;

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
     * @var bool
     */
    public $compiled;

    /**
     * @var string
     */
    public $component;

    /**
     * @var int
     */
    public $max = 999;

    /**
     * @var bool
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
    public $renderNamespace;

    /**
     * @var string
     */
    public $contents;

    /**
     * @var array
     */
    public $rules = [];

    /**
     * @var array
     */
    public $rulesForTranslatedFields = [];

    /**
     * Renderedata.
     */
    public ?RenderData $renderData = null;

    /**
     * @var TwillBlockComponent
     */
    public ?string $componentClass = null;

    public ?InlineRepeater $inlineRepeater = null;

    /**
     * @param class-string<TwillBlockComponent> $componentClass
     */
    public static function forComponent(string $componentClass, string $source = self::SOURCE_APP): self
    {
        $class = new self(
            file: null,
            type: 'block',
            source: $source,
            name: $componentClass::getBlockIdentifier(),
            componentClass: $componentClass
        );

        $class->group = $componentClass::getBlockGroup();
        $class->title = $componentClass::getBlockTitle();
        $class->icon = $componentClass::getBlockIcon();
        $class->titleField = $componentClass::getBlockTitleField();
        $class->hideTitlePrefix = $componentClass::shouldHidePrefix();
        $class->rulesForTranslatedFields = (new $componentClass())->getTranslatableValidationRules();
        $class->rules = (new $componentClass())->getValidationRules();

        return $class;
    }

    /**
     * Make a block instance out of arguments.
     *
     * @param $file
     * @param $type
     * @param $source
     * @param $name
     *   Mainly for packages, but this will get the preview/render view file from that namespace.
     * @return static
     */
    public static function make($file, $type, $source, $name = null, string $renderNamespace = null): self
    {
        $name = $name ?? Str::before(
            $file->getFilename(),
            '.blade.php'
        );

        $transformed = Str::studly($name) . 'Block';
        // @todo: Package block classes?
        $className = "\App\Twill\Block\\$transformed";
        if (class_exists($className)) {
            return new $className($file, $type, $source, $name);
        }

        return new self($file, $type, $source, $name, $renderNamespace);
    }

    public function newInstance(): static
    {
        return new static(
            $this->file,
            $this->type,
            $this->source,
            $this->name,
            $this->renderNamespace,
            $this->componentClass,
            $this->inlineRepeater
        );
    }

    public function getPosition(): float|int|string
    {
        return $this->componentClass && is_callable([$this->componentClass, 'getPosition']) ? $this->componentClass::getPosition() : 0;
    }

    /**
     * Gets the first match being a block or repeater.
     */
    public static function findFirstWithType(string $type): ?self
    {
        return TwillBlocks::getBlockCollection()->findByName($type);
    }

    public static function getForType(string $type, bool $repeater = false): self
    {
        if ($repeater) {
            $blocksList = TwillBlocks::getRepeaters();
        } else {
            // Here we include the settings blocks as well.
            $blocksList = TwillBlocks::getBlocks(true);
        }

        return $blocksList->first(function (self $blockConfig) use ($type) {
            return $blockConfig->name === $type;
        });
    }

    public static function getForComponent(string $type, bool $repeater = false): ?self
    {
        if ($repeater) {
            $blocksList = TwillBlocks::getRepeaters();
        } else {
            // Here we include the settings blocks as well.
            $blocksList = TwillBlocks::getBlocks(true);
        }

        return $blocksList->first(function (self $blockConfig) use ($type) {
            return $blockConfig->component === $type;
        });
    }

    /**
     * Block constructor.
     * @param Symfony\Component\Finder\SplFileInfo|null $file
     * @param string|null $type
     * @param $source
     * @param $name
     * @param string $renderNamespace
     *   Mainly for packages, but this will get the preview/render view file from that namespace.
     * @param InlineRepeater $inlineRepeater used when registering dynamic repeaters.
     * @throws \Exception
     */
    final public function __construct(
        $file,
        $type,
        $source,
        $name = null,
        ?string $renderNamespace = null,
        ?string $componentClass = null,
        ?InlineRepeater $inlineRepeater = null
    ) {
        $this->file = $file;

        $this->type = $type;

        $this->source = $source;

        $this->componentClass = $componentClass;

        $this->inlineRepeater = $inlineRepeater;

        if (! $this->componentClass) {
            $this->fileName = $this->file ? $this->file->getPathName() : 'Custom vue file';
        }

        $this->renderNamespace = $renderNamespace;

        $this->name = $name ?? Str::before(
            $this->file->getFilename(),
            '.blade.php'
        );

        // @todo: This may not be needed.
        if ($type === self::TYPE_BLOCK && config('twill.block_editor.repeaters.' . $this->name) !== null) {
            $this->type = self::TYPE_REPEATER;
        }

        if (! $inlineRepeater) {
            $this->parse();
        }
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getData(array $data, \A17\Twill\Models\Block $block): array
    {
        return $data;
    }

    /**
     * Gets the form data. This is only called once and not per create.
     *
     * This function is not aware of the context. If you need to know the current module you have to figure that out
     * yourself by for example parsing the route.
     */
    public function getFormData(): array
    {
        return [];
    }

    public function toList(): Collection
    {
        return collect([
            'title' => $this->title,
            'titleField' => $this->titleField,
            'hideTitlePrefix' => $this->hideTitlePrefix,
            'trigger' => $this->trigger,
            'selectTrigger' => $this->selectTrigger,
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
            'max' => $this->type === self::TYPE_REPEATER ? $this->max : null,
        ]);
    }

    public function toShortList(): Collection
    {
        return collect([
            'title' => $this->title,
            'name' => $this->name,
            'group' => $this->group,
            'type' => $this->type,
            'icon' => $this->icon,
        ]);
    }

    public function makeName(string $name): string
    {
        return Str::kebab($name);
    }

    /**
     * @throws \Exception
     */
    public function parse(): self
    {
        $contents = $this->file ? file_get_contents($this->file->getPathName()) : '';

        $this->title = $this->parseProperty('title', $contents, $this->name);
        $this->trigger = $this->parseProperty(
            'trigger',
            $contents,
            $this->name,
            $this->type === self::TYPE_REPEATER ? twillTrans('twill::lang.fields.block-editor.add-item') : null
        );
        $this->selectTrigger = $this->parseProperty(
            'SelectTrigger',
            $contents,
            $this->name,
            $this->type === self::TYPE_REPEATER ? twillTrans('twill::lang.fields.block-editor.select-existing') : null
        );
        $this->max = (int)$this->parseProperty('max', $contents, $this->name, 999);
        $this->group = $this->parseProperty('group', $contents, $this->name, 'app');
        $this->icon = $this->parseProperty('icon', $contents, $this->name, 'text');
        $this->compiled = (bool)$this->parseProperty('compiled', $contents, $this->name, false);
        $this->component = $this->parseProperty('component', $contents, $this->name, "a17-block-{$this->name}");
        $this->isNewFormat = $this->isNewFormat($contents);
        $this->contents = $contents;

        $this->parseArrayProperty('ValidationRules', $contents, $this->name, function ($value) {
            $this->rules = $value ?? $this->rules;
        });

        $this->parseArrayProperty('ValidationRulesForTranslatedFields', $contents, $this->name, function ($value) {
            $this->rulesForTranslatedFields = $value ?? $this->rulesForTranslatedFields;
        });

        $this->parseMixedProperty('titleField', $contents, $this->name, function ($value, $options) {
            $this->titleField = $value;
            $this->hideTitlePrefix = (bool)($options['hidePrefix'] ?? false);
        });

        return $this;
    }

    /**
     * Checks both the blade file or helper class for validation rules. Returns in order the first one with data.
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Checks both the blade file or helper class for validation rules. Returns in order the first one with data.
     */
    public function getRulesForTranslatedFields(): array
    {
        return $this->rulesForTranslatedFields;
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
            preg_match("/@{$pattern}{$bladeProperty}" . self::PREG_REPLACE_INNER . '/', $block, $matches);

            if (filled($matches)) {
                $result = $matches[1];

                $result = Str::replaceLast(')', '', Str::replaceFirst('(', '', $result));

                // Process the match if it is __(translatable).
                if (Str::startsWith($result, '__(')) {
                    return twillTrans(preg_replace('/__\((?:"|\')(.*)(?:"|\')\)/', '$1', $result));
                }

                // Process the match if it is twillTrans(translatable).
                if (Str::startsWith($result, 'twillTrans(')) {
                    return twillTrans(preg_replace('/twillTrans\((?:"|\')(.*)(?:"|\')\)/', '$1', $result));
                }

                return trim($result, '\'"');
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
     * @param callable $callback Should have the following signature: `function (array $value)`
     * @return void
     * @throws \Exception
     */
    public function parseArrayProperty(
        $property,
        $block,
        $blockName,
        $callback
    ): void {
        $this->parseMixedProperty($property, $block, $blockName, function ($value) use ($callback) {
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
     * @param callable $callback Should have the following signature: `function ($value, $options)`
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
        }

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

        if (
            $configBlock = collect(config('twill.block_editor.blocks'))->filter(
                function ($block) use ($blockName) {
                    return Str::contains($block['component'], $blockName);
                }
            )->first()
        ) {
            if ($value = ($configBlock[$property] ?? null)) {
                return $value;
            }
        }
        if (
            $configRepeater = collect(config('twill.block_editor.repeaters'))->filter(
                function ($repeater) use ($blockName) {
                    return Str::contains($repeater['component'], $blockName);
                }
            )->first()
        ) {
            if ($value = ($configRepeater[$property] ?? null)) {
                return $value;
            }
        }

        if ($property === 'title' && $this->componentClass) {
            return $this->componentClass::getBlockTitle();
        }

        if ($property !== 'title') {
            return $default;
        }

        if ($this->{$property} !== null) {
            return $this->{$property};
        }

        // Title is mandatory
        throw new Exception(
            "Block {$blockName} does not exists or the mandatory property '{$property}' " .
            'was not found on this block. If you are still using blocks on the twill.php ' .
            'file, please check if the block is present and properly configured.'
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
        return $this->file ? $this->file->getFileName() : $this->componentClass;
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function renderForm()
    {
        View::share('TwillUntilConsumed', ['renderForBlocks' => true]);
        if ($this->componentClass) {
            $block = (new $this->componentClass())->renderForm();
        } elseif ($this->inlineRepeater) {
            $block = $this->inlineRepeater->renderForm();
        } else {
            $block = BladeCompiler::render(
                $this->contents,
                [
                    'renderForBlocks' => true,
                ] + $this->getFormData()
            );
        }
        View::share('TwillUntilConsumed', []);

        return $block;
    }

    public function getBlockView($blockViewMappings = [])
    {
        if ($this->renderNamespace) {
            $view = $this->renderNamespace . '::' . $this->name;
        } else {
            $view = config('twill.block_editor.block_views_path') . '.' . $this->name;
        }

        if (array_key_exists($this->name, $blockViewMappings)) {
            $view = $blockViewMappings[$this->name];
        }

        return $view;
    }

    public function setRenderData(RenderData $renderData): void
    {
        $this->renderData = $renderData;
    }

    public function block(): ?\A17\Twill\Models\Block
    {
        return $this->renderData?->block;
    }

    public function renderView(
        array $blockViewMappings,
        array $data,
        bool $inEditor = false
    ): string {
        if (! $this->renderData) {
            throw new \Exception('Cannot render without renderData');
        }

        $data['inEditor'] = $inEditor;

        $view = $this->getBlockView($blockViewMappings);

        $data = Container::getInstance()->call([$this, 'getData'], ['data' => $data, 'block' => $this->renderData->block]);

        $data['block'] = $this->renderData->block;
        $data['renderData'] = $this->renderData;

        if ($this->componentClass) {
            return Blade::renderComponent($this->componentClass::forRendering($this->renderData->block, $this->renderData, $inEditor));
        }

        try {
            return view($view, $data)->render();
        } catch (Exception $e) {
            if (config('twill.debug')) {
                $error = $e->getMessage() . ' in ' . $e->getFile();

                return View::make('twill::errors.block', ['view' => $view, 'error' => $error])->render();
            }
        }

        return '';
    }
}
