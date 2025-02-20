<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait HasMax
{
    protected ?int $max = null;

    /**
     * Sets the max amount of items.
     */
    public function max(int $max): static
    {
        $this->max = $max;

        return $this;
    }
}
