<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait isTranslatable
{
    protected bool $translated = false;

    public function translatable(bool $translatable = true): self
    {
        $this->translated = $translatable;
        return $this;
    }
}
