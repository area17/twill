<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait IsTranslatable
{
    protected bool $translated = false;

    /**
     * Makes the field translatable.
     */
    public function translatable(bool $translatable = true): static
    {
        $this->translated = $translatable;

        return $this;
    }
}
