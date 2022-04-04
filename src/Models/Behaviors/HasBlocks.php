<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Helpers\BlockRenderer;

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
        return BlockRenderer::fromEditor($this, $name)->render($blockViewMappings, $data, $renderChilds);
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
        return BlockRenderer::fromEditor($this, 'default')->render($blockViewMappings, $data, $renderChilds);
    }
}
