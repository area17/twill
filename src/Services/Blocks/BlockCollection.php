<?php

namespace A17\Twill\Services\Blocks;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class BlockCollection extends Collection
{
    public function findByName($search, $sources = [])
    {
        $block = new Block();

        $name = $block->makeName($search);

        return (new static($this->items))
            ->filter(function ($block) use ($search, $sources) {
                return $block->name == $search &&
                    (blank($sources) ||
                        collect($sources)->contains($block->source));
            })
            ->sortBy(function ($block) {
                return $block->source === 'app' ? 0 : 1;
            })
            ->first();
    }
}
