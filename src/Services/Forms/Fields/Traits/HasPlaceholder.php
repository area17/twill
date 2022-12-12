<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait HasPlaceholder
{
    protected ?string $placeholder = null;

    /**
     * Sets the placeholder of the field.
     */
    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }
}
