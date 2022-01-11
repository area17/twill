<?php

namespace A17\Twill\Helpers;

use A17\Twill\Models\Block;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

abstract class TwillBlock
{
    /**
     * @var array
     *
     * The validation rules for this block.
     */
    protected $rules = [];

    public function __construct()
    {
    }

    public function getData(Block $block, array $data): array
    {
        return $data;
    }

    public function validate(array $formData): void
    {
        if (!empty($this->rules)) {
            Validator::validate($formData, $this->rules);
        }

        return;
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
