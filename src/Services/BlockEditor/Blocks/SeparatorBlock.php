<?php

namespace A17\CmsToolkit\Services\BlockEditor\Blocks;

class SeparatorBlock extends BaseBlock
{
    protected $types = [
        'blockseparator',
    ];

    public function blockseparatorToHtml()
    {
        return view('front.blocks.separator');
    }
}
