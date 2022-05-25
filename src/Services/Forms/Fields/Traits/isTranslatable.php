<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait isTranslatable
{
    protected bool $translated = false;

    /**
     * Makes the field translatable.
     */
    public function translatable(bool $translatable = true): self
    {
        $this->translated = $translatable;
        return $this;
    }
}
