<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait hasMax
{
    protected ?int $max = null;

    public function max(int $max): self
    {
        $this->max = $max;

        return $this;
    }
}
