<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait canHaveButtonOnTop
{
    protected bool $buttonOnTop = false;

    /**
     * Shows the browse button above instead of below the list of items.
     */
    public function buttonOnTop(bool $buttonOnTop = true): self
    {
        $this->buttonOnTop = $buttonOnTop;

        return $this;
    }
}
