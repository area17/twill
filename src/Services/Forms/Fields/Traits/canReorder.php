<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait canReorder
{
    protected bool $reorder = true;

    public function disableReorder(bool $disableReorder = true): self
    {
        $this->reorder = !$disableReorder;

        return $this;
    }
}
