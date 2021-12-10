<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\Block;

trait HasBlocks
{
    /**
     * Defines the one-to-many relationship for block objects.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function blocks()
    {
        return $this->morphMany(Block::class, 'blockable')->orderBy(config('twill.blocks_table', 'twill_blocks') . '.position', 'asc');
    }

    /**
     * Returns the rendered Blade views for all attached blocks in their proper order.
     *
     * @param bool $renderChilds Include all child blocks in the rendered output.
     * @param array $blockViewMappings Provide alternate Blade views for blocks. Format: `['block-type' => 'view.path']`.
     * @param array $data Provide extra data to Blade views.
     * @return string
     */
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
