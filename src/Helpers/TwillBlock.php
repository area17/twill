<?php

namespace A17\Twill\Helpers;

use A17\Twill\Models\Block;
use Illuminate\Support\Str;

abstract class TwillBlock
{
    /**
     * @var array
     */
    public $data;

    /**
     * @var \A17\Twill\Models\Block
     */
    public $block;

    public function __construct(Block $block, array $data)
    {
        $this->data = $data;
        $this->block = $block;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public static function getBlockClass(string $view, Block $block, array $data): ?TwillBlock
    {
        $exploded = explode('.', $view);
        $transformed = Str::studly(array_pop($exploded)) . 'Block';
        $className = "\App\Twill\Block\\$transformed";
        if (class_exists($className)) {
            return new $className($block, $data);
        }
        return null;
    }
}
