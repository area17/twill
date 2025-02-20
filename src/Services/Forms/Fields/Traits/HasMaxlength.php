<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait HasMaxlength
{
    protected ?int $maxlength = null;

    /**
     * Sets the max character length.
     */
    public function maxLength(string $maxlength): static
    {
        $this->maxlength = $maxlength;

        return $this;
    }
}
