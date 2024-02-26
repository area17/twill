<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Helpers\BlockRenderer;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasBlocks
{
    /**
     * Defines the one-to-many relationship for block objects.
     *
     * @return MorphMany
     */
    public function blocks()
    {
        return $this->morphMany(twillModel('block'), 'blockable')->orderBy('position');
    }

    public function renderNamedBlocks($name = 'default', $blockViewMappings = [], $data = [])
    {
        return BlockRenderer::fromEditor($this, $name)->render($blockViewMappings, $data);
    }

    /**
     * Returns the rendered Blade views for all attached blocks in their proper order.
     *
     * @param  array  $blockViewMappings  Provide alternate Blade views for blocks.
     *                                    Format: `['block-type' => 'view.path']`.
     * @param  array  $data  Provide extra data to Blade views.
     * @return string
     */
    public function renderBlocks($blockViewMappings = [], $data = [])
    {
        return BlockRenderer::fromEditor($this, 'default')->render($blockViewMappings, $data);
    }
}
