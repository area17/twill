<?php

namespace A17\CmsToolkit\Services\BlockEditor\Blocks;

class Separator extends BaseBlock
{
    protected $types = [
        'blockseparator',
    ];

    public function blockseparatorToHtml()
    {
        return view($this->view('separator'));
    }
}
