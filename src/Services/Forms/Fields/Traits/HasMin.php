<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait HasMin
{
    protected ?int $min = null;

    /**
     * Sets the minimum amount of items.
     */
    public function min(int $min): static
    {
        $this->min = $min;

        return $this;
    }
}
