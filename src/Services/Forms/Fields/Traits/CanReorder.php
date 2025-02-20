<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait CanReorder
{
    protected bool $reorder = true;

    /**
     * Disables the reordering of items.
     */
    public function disableReorder(bool $disableReorder = true): static
    {
        $this->reorder = !$disableReorder;

        return $this;
    }
}
