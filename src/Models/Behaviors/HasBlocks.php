<?php

namespace A17\CmsToolkit\Models\Behaviors;

use A17\CmsToolkit\Models\Block;

trait HasBlocks
{
    public function blocks()
    {
        return $this->morphMany(Block::class, 'blockable')->orderBy('blocks.position', 'asc');
    }

    public function renderBlocks($renderChilds = true, $blockViewMappings = [])
    {
        return $this->blocks->where('parent_id', null)->map(function ($block) use ($blockViewMappings, $renderChilds) {
            if ($renderChilds) {
                $childBlocks = $this->blocks->where('parent_id', $block->id);

                $renderedChildViews = $childBlocks->map(function ($childBlock) use ($blockViewMappings) {
                    $view = $this->getBlockView($childBlock->type, $blockViewMappings);
                    return view($view)->with('block', $childBlock)->render();
                })->implode('');
            } else {
                $block->childs = $this->blocks->where('parent_id', $block->id);
            }

            $view = $this->getBlockView($block->type, $blockViewMappings);

            return view($view)->with('block', $block)->render() . ($renderedChildViews ?? '');
        })->implode('');
    }

    private function getBlockView($blockType, $blockViewMappings = [])
    {
        $view = config('cms-toolkit.block_editor.block_views_path') . '.' . $blockType;

        if (array_key_exists($blockType, $blockViewMappings)) {
            $view = $blockViewMappings[$blockType];
        }

        return $view;
    }
}
