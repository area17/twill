<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

use A17\Twill\Services\Forms\Option;
use A17\Twill\Services\Forms\Options;

trait hasOptions
{
    protected Options|null $options = null;

    protected int $columns = 0;

    /**
     * List of options to display in the field.
     */
    public function options(Options $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Adds a single option.
     */
    public function addOption(Option $option): self
    {
        if ($this->options === null) {
            $this->options = Options::make();
        }
        $this->options->add($option);

        return $this;
    }
}
