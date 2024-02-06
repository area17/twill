<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait HasDirection
{
    protected ?string $direction = null;

    /**
     * Sets the direction of the field.
     */
    public function direction(string $direction): static
    {
        $this->direction = $direction === 'ltr' || $direction === 'rtl' ? $direction : 'auto';

        return $this;
    }
}
