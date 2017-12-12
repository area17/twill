<?php

namespace A17\CmsToolkit\Models\Behaviors;

use A17\CmsToolkit\Models\Block;

trait HasBlocks
{
    public function blocks()
    {
        return $this->morphMany(Block::class, 'blockable')->orderBy('blocks.position', 'asc');
    }

    public function renderBlocks($blockViewMappings = [])
    {
        return $this->blocks->where('parent_id', null)->map(function ($block) use ($blockViewMappings) {
            $childBlocks = $this->blocks->where('parent_id', $block->id);

            $renderedChildViews = $childBlocks->map(function ($childBlock) use ($blockViewMappings) {
                $view = $this->getBlockView($childBlock->type, $blockViewMappings);
                return view($view)->with('block', $childBlock)->render();
            })->implode('');

            $view = $this->getBlockView($block->type, $blockViewMappings);

            return view($view)->with('block', $block)->render() . $renderedChildViews;
        })->implode('');
    }

    private function getBlockView($blockType, $blockViewMappings = [])
    {
        $view = config('cms-toolkit.block_editor.render_views_namespace') . '.' . $blockType;

        if (array_key_exists($blockType, $blockViewMappings)) {
            $view = $blockViewMappings[$blockType];
        }

        return $view;
    }
}
