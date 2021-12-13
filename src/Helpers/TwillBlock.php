<?php

namespace A17\Twill\Helpers;

use A17\Twill\Models\Block;

abstract class TwillBlock
{
    public array $data;
    public Block $block;

    public function __construct(Block $block, array $data)
    {
        $this->data = $data;
        $this->block = $block;
    }

    public function getData(): array
    {
        return [];
    }
}
