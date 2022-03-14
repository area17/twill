<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\Block;
use A17\Twill\Services\Blocks\Block as BlockConfig;

trait HasBlocks
{
    /**
     * Defines the one-to-many relationship for block objects.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function blocks()
    {
        return $this->morphMany(twillModel('block'), 'blockable')->orderBy(
            config('twill.blocks_table', 'twill_blocks') . '.position',
            'asc'
        );
    }

    public function renderNamedBlocks($name = 'default', $renderChilds = true, $blockViewMappings = [], $data = [])
    {
        return $this->blocks
            ->filter(function ($block) use ($name) {
                return $name === 'default'
                    ? ($block->editor_name === $name || $block->editor_name === null)
                    : $block->editor_name === $name;
            })
            ->where('parent_id', null)
            ->map(function ($block) use ($blockViewMappings, $renderChilds, $data) {
                if ($renderChilds) {
                    $childBlocks = $this->blocks->where('parent_id', $block->id);

                    $renderedChildViews = $childBlocks->map(function ($childBlock) use ($blockViewMappings, $data) {
                        $class = BlockConfig::getForType($childBlock->type);
                        $view = $class->getBlockView($blockViewMappings);
                        $data = $class->getData($data, $childBlock);

                        return view($view, $data)->with('block', $childBlock)->render();
                    })->implode('');
                }

                $block->childs = $this->blocks->where('parent_id', $block->id);

                $class = BlockConfig::getForType($block->type);
                $view = $class->getBlockView($blockViewMappings);
                $data = $class->getData($data, $block);

                return view($view, $data)->with('block', $block)->render() . ($renderedChildViews ?? '');
            })->implode('');
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
        return $this->renderNamedBlocks('default', $renderChilds, $blockViewMappings, $data);
    }
}
