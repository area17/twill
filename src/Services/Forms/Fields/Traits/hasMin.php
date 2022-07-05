<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait hasMin
{
    protected ?int $min = null;

    /**
     * Sets the minimum amount of items.
     */
    public function min(int $min): self
    {
        $this->min = $min;

        return $this;
    }
}
