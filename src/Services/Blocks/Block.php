<?php

namespace A17\Twill\Services\Blocks;

use A17\Twill\Facades\TwillBlocks;
use Exception;
use Illuminate\Support\Str;

/**
 * @todo(3.x): This is not really a service, and we should move this to another location.
 */
class Block
{
    /**
     * @var string
     */
    public const SOURCE_APP = 'app';

    /**
     * @var string
     */
    public const SOURCE_TWILL = 'twill';

    /**
     * @var string
     */
    public const SOURCE_CUSTOM = 'custom';

    /**
     * @var string
     */
    public const TYPE_BLOCK = 'block';

    /**
     * @var string
     */
    public const TYPE_REPEATER = 'repeater';

    /**
     * @var string
     */
    public const PREG_REPLACE_INNER = '(?:\'|")(.*)(?:\'|")';

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
    public $hideTitlePrefix = false;

    /**
     * @var string
     */
    public $trigger;

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
    public $icon;

    /**
     * @var bool
     */
    public $compiled = false;

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
    public $isNewFormat = false;

    /**
     * @var string
     */
    public $fileName;

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
     * Make a block instance out of arguments.
     *
     * @param $file
     * @param $type
     * @param $source
     * @param $name
     * @param string $renderNamespace
     *   Mainly for packages, but this will get the preview/render view file from that namespace.
     */
    public static function make($file, $type, $source, $name = null, string $renderNamespace = null): \A17\Twill\Services\Blocks\Block
    {
        $name = $name ?? Str::before(
            $file->getFilename(),
            '.blade.php'
        );

        $transformed = Str::studly($name) . 'Block';
        // @todo: Package block classes?
        $className = sprintf('\App\Twill\Block\%s', $transformed);
        if (class_exists($className)) {
            return new $className($file, $type, $source, $name);
        }

        return new self($file, $type, $source, $name, $renderNamespace);
    }

    /**
     * Gets the first match being a block or repeater.
     */
    public static function findFirstWithType(string $type): ?self
    {
        return app(BlockCollection::class)->findByName($type);
    }

    public static function getForType(string $type, bool $repeater = false): self
    {
        $blocksList = $repeater ? TwillBlocks::getRepeaters() : TwillBlocks::getBlocks();

        return $blocksList->first(function (self $blockConfig) use ($type): bool {
            return $blockConfig->name === $type;
        });
    }

    public static function getForComponent(string $type, bool $repeater = false): self
    {
        $blocksList = $repeater ? TwillBlocks::getRepeaters() : TwillBlocks::getBlocks();

        return $blocksList->first(function (self $blockConfig) use ($type): bool {
            return $blockConfig->component === $type;
        });
    }

    /**
     * Block constructor.
     * @param Symfony\Component\Finder\SplFileInfo $file
     * @param $type
     * @param $source
     * @param $name
     * @param string $renderNamespace
     *   Mainly for packages, but this will get the preview/render view file from that namespace.
     * @throws \Exception
     */
    public function __construct(public $file, /**
     * @var string
     */
    public $type, /**
     * @var string
     */
    public $source, $name = null, public ?string $renderNamespace = null)
    {
        // @change: This now holds the full file path instead of just the fileName.
        $this->fileName = $this->file ? $this->file->getPathName() : 'Custom vue file';

        $this->name = $name ?? Str::before(
            $this->file->getFilename(),
            '.blade.php'
        );

        // @todo: This may not be needed.
        if ($type === self::TYPE_BLOCK && config('twill.block_editor.repeaters.' . $this->name) !== null) {
            $this->type = self::TYPE_REPEATER;
        }

        $this->parse();
    }

    /**
     * @param $source
     * @return $this
     */
    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getData(array $data, \A17\Twill\Models\Block $block): array
    {
        return $data;
    }

    /**
     * Gets the form data. This is only called once and not per create.
     *
     * This function is not aware of the context. If you need to know the current module you have to figure that out
     * yourself by for example parsing the route.
     *
     * @return mixed[]
     */
    public function getFormData(): array
    {
        return [];
    }

    public function toList(): \Illuminate\Support\Collection
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
            'max' => $this->type === self::TYPE_REPEATER ? $this->max : null,
        ]);
    }

    public function toShortList(): \Illuminate\Support\Collection
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
     */
    public function makeName($name): string
    {
        return Str::kebab($name);
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function parse(): static
    {
        $contents = $this->file ? file_get_contents($this->file->getPathName()) : '';

        $this->title = $this->parseProperty('title', $contents, $this->name);
        $this->trigger = $this->parseProperty('trigger', $contents, $this->name, $this->type === self::TYPE_REPEATER ? twillTrans('twill::lang.fields.block-editor.add-item') : null);
        $this->max = (int) $this->parseProperty('max', $contents, $this->name, 999);
        $this->group = $this->parseProperty('group', $contents, $this->name, 'app');
        $this->icon = $this->parseProperty('icon', $contents, $this->name, 'text');
        $this->compiled = (bool) $this->parseProperty('compiled', $contents, $this->name, false);
        $this->component = $this->parseProperty('component', $contents, $this->name, sprintf('a17-block-%s', $this->name));
        $this->isNewFormat = $this->isNewFormat($contents);
        $this->contents = $contents;

        $this->parseArrayProperty('ValidationRules', $contents, $this->name, function ($value): void {
            $this->rules = $value ?? $this->rules;
        });

        $this->parseArrayProperty('ValidationRulesForTranslatedFields', $contents, $this->name, function ($value): void {
            $this->rulesForTranslatedFields = $value ?? $this->rulesForTranslatedFields;
        });

        $this->parseMixedProperty('titleField', $contents, $this->name, function ($value, $options): void {
            $this->titleField = $value;
            $this->hideTitlePrefix = (bool) ($options['hidePrefix'] ?? false);
        });

        return $this;
    }

    /**
     * Checks both the blade file or helper class for validation rules. Returns in order the first one with data.
     * @return mixed[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Checks both the blade file or helper class for validation rules. Returns in order the first one with data.
     * @return mixed[]
     */
    public function getRulesForTranslatedFields(): array
    {
        return $this->rulesForTranslatedFields;
    }

    /**
     * Parse a string property directive in the form of `@twillTypeProperty('value')`.
     *
     * @param string|null $default
     * @return string
     * @throws \Exception
     */
    public function parseProperty(
        string $property,
        string $block,
        string $blockName,
        $default = null
    ) {
        $bladeProperty = ucfirst($property);

        foreach (['twillProp', 'twillBlock', 'twillRepeater'] as $pattern) {
            preg_match(sprintf('/@%s%s\(', $pattern, $bladeProperty) . self::PREG_REPLACE_INNER . "\)/", $block, $matches);

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
     * @param callable $callback  Should have the following signature: `function (array $value)`
     * @throws \Exception
     */
    public function parseArrayProperty(
        string $property,
        string $block,
        string $blockName,
        callable $callback
    ): void {
        $this->parseMixedProperty($property, $block, $blockName, function ($value) use ($callback): void {
            $callback($value);
        });
    }

    /**
     * Parse a mixed property directive in the form of `@twillTypeProperty('value', [...])`
     * and pass the result to a given callback.
     *
     * @param callable $callback  Should have the following signature: `function ($value, $options)`
     * @return mixed
     * @throws \Exception
     */
    public function parseMixedProperty(
        string $property,
        string $block,
        string $blockName,
        callable $callback
    ) {
        $bladeProperty = ucfirst($property);

        foreach (['twillProp', 'twillBlock', 'twillRepeater'] as $pattern) {
            // Regexp modifiers:
            //   `s`  allows newlines as part of the `.*` match
            //   `U`  stops the match at the first closing parenthesis
            preg_match(sprintf('/@%s%s\((.*)\)/sU', $pattern, $bladeProperty), $block, $matches);

            if (filled($matches)) {
                // Wrap the match in array notation and feed it to `eval` to get an actual array.
                // In this context, we're only interested in the first two possible values.
                $content = sprintf('[%s]', $matches[1]);
                $parsedContent = eval(sprintf('return %s;', $content));
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
                sprintf('twill.block_editor.blocks.%s.%s', $blockName, $property)
            )
        ) {
            return $value;
        }

        if (
            $value = config(
                sprintf('twill.block_editor.repeaters.%s.%s', $blockName, $property)
            )
        ) {
            return $value;
        }

        if (
            ($configBlock = collect(config('twill.block_editor.blocks'))->filter(
                function ($block) use ($blockName): bool {
                    return Str::contains($block['component'], $blockName);
                }
            )->first()) && ($value = ($configBlock[$property] ?? null))
        ) {
            return $value;
        }

        if (
            ($configRepeater = collect(config('twill.block_editor.repeaters'))->filter(
                function ($repeater) use ($blockName): bool {
                    return Str::contains($repeater['component'], $blockName);
                }
            )->first()) && ($value = ($configRepeater[$property] ?? null))
        ) {
            return $value;
        }

        if ($property !== 'title') {
            return $default;
        }

        // Title is mandatory
        throw new Exception(
            sprintf('Block %s does not exists or the mandatory property \'%s\' ', $blockName, $property) .
            'was not found on this block. If you are still using blocks on the twill.php ' .
            'file, please check if the block is present and properly configured.'
        );
    }

    /**
     * @param $block
     */
    public function isNewFormat($block): bool
    {
        preg_match("#@twill(Prop|Block|Repeater).*\('(.*)'\)#", $block, $propMatches);

        return filled($propMatches);
    }

    public function getFileName(): string
    {
        return $this->file ? $this->file->getFileName() : 'Custom Vue file';
    }

    /**
     * @throws \Throwable
     */
    public function render(): bool|string
    {
        return BladeCompiler::render(
            self::removeSpecialBladeTags($this->contents),
            [
                'renderForBlocks' => true,
            ] + $this->getFormData()
        );
    }

    /**
     * @param $contents
     * @return string
     */
    public static function removeSpecialBladeTags($contents)
    {
        return preg_replace([
            "/@twillProp.*\(" . self::PREG_REPLACE_INNER . "\)/sU",
            "/@twillBlock.*\(" . self::PREG_REPLACE_INNER . "\)/sU",
            "/@twillRepeater.*\(" . self::PREG_REPLACE_INNER . "\)/sU",
        ], '', $contents);
    }

    public function getBlockView($blockViewMappings = [])
    {
        if ($this->renderNamespace !== '' && $this->renderNamespace !== '0') {
            $view = $this->renderNamespace . '::' . $this->name;
        } else {
            $view = config('twill.block_editor.block_views_path') . '.' . $this->name;
        }

        if (array_key_exists($this->name, $blockViewMappings)) {
            $view = $blockViewMappings[$this->name];
        }

        return $view;
    }
}
