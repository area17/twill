<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait hasBorder
{
    protected bool $border = false;

    /**
     * Adds a border around the options.
     */
    public function border(bool $border = true): self
    {
        $this->border = $border;

        return $this;
    }
}
