<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait canReorder
{
    protected bool $reorder = true;

    /**
     * Disables the reordering of items.
     */
    public function disableReorder(bool $disableReorder = true): self
    {
        $this->reorder = !$disableReorder;

        return $this;
    }
}
