<?php

namespace A17\Twill\Helpers;

use A17\Twill\Models\Block;
use Illuminate\Support\Str;

abstract class TwillBlock
{
    /**
     * @var array
     *
     * The validation rules for this block.
     */
    protected $rules = [];

    /**
     * @var array
     *
     * The validation rules for this block.
     */
    protected $rulesForTranslatedFields = [];

    public function __construct()
    {
    }

    public function getData(Block $block, array $data): array
    {
        return $data;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getRulesForTranslatedFields(): array
    {
        return $this->rulesForTranslatedFields;
    }

    public static function getBlockClassForName(string $name): ?TwillBlock
    {
        $transformed = Str::studly($name) . 'Block';
        $className = "\App\Twill\Block\\$transformed";
        if (class_exists($className)) {
            return new $className();
        }

        return null;
    }

    public static function getBlockClassForView(string $view): ?TwillBlock
    {
        $exploded = explode('.', $view);

        return self::getBlockClassForName(array_pop($exploded));
    }
}
