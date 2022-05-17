<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait hasPlaceholder
{
    protected ?string $placeholder = null;

    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }
}
