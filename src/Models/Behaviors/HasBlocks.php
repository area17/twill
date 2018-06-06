<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\Block;

trait HasBlocks
{
    public function blocks()
    {
        return $this->morphMany(Block::class, 'blockable')->orderBy('blocks.position', 'asc');
    }

    public function renderBlocks($renderChilds = true, $blockViewMappings = [], $data = [])
    {
        return $this->blocks->where('parent_id', null)->map(function ($block) use ($blockViewMappings, $renderChilds, $data) {
            if ($renderChilds) {
                $childBlocks = $this->blocks->where('parent_id', $block->id);

                $renderedChildViews = $childBlocks->map(function ($childBlock) use ($blockViewMappings, $data) {
                    $view = $this->getBlockView($childBlock->type, $blockViewMappings);
                    return view($view, $data)->with('block', $childBlock)->render();
                })->implode('');
            }

            $block->childs = $this->blocks->where('parent_id', $block->id);

            $view = $this->getBlockView($block->type, $blockViewMappings);

            return view($view, $data)->with('block', $block)->render() . ($renderedChildViews ?? '');
        })->implode('');
    }

    private function getBlockView($blockType, $blockViewMappings = [])
    {
        $view = config('twill.block_editor.block_views_path') . '.' . $blockType;

        if (array_key_exists($blockType, $blockViewMappings)) {
            $view = $blockViewMappings[$blockType];
        }

        return $view;
    }
}
