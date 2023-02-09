<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait HasBorder
{
    protected bool $border = false;

    /**
     * Adds a border around the options.
     */
    public function border(bool $border = true): static
    {
        $this->border = $border;

        return $this;
    }
}
