<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait hasMaxlength
{
    protected ?int $maxlength = null;

    /**
     * Sets the max character length.
     */
    public function maxLength(string $maxlength): self
    {
        $this->maxlength = $maxlength;
        return $this;
    }
}
