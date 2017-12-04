<?php

namespace A17\CmsToolkit\Models\Behaviors;

use A17\CmsToolkit\Models\Block;

trait HasBlocks
{
    public function blocks()
    {
        return $this->morphMany(Block::class, 'blockable')->orderBy('blocks.position', 'asc');
    }
}
