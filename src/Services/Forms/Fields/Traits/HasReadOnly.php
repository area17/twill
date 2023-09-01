<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait HasReadOnly
{
    protected ?bool $readOnly = false;

    /**
     * Marks the field as read only.
     */
    public function readOnly(bool $readOnly = true): static
    {
        $this->readOnly = $readOnly;

        return $this;
    }
}
