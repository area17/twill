<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait DisableActions
{
    protected bool $displayActions = true;

    /**
     * Adds a border around the options.
     */
    public function disableActions(bool $displayActions = true): static
    {
        $this->displayActions = !$displayActions;

        return $this;
    }
}
