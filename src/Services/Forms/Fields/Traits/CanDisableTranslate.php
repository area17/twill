<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait CanDisableTranslate
{
    /**
     * @var bool
     */
    protected bool $disableTranslate = false;

    /**
     * Disables translate
     *
     * @return $this
     */
    public function disableTranslate(): static
    {
        $this->disableTranslate = true;

        return $this;
    }
}
