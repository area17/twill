<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait hasMin
{
    protected ?int $min = null;

    public function min(int $min): self
    {
        $this->min = $min;

        return $this;
    }
}
