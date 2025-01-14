<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait DisableActions
{
    protected bool $actions = true;

    /**
     * Adds a border around the options.
     */
    public function disableActions(bool $actions = true): static
    {
        $this->displayActions = !$actions;

        return $this;
    }
}
