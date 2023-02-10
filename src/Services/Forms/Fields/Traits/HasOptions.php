<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

use A17\Twill\Services\Forms\Option;
use A17\Twill\Services\Forms\Options;

trait HasOptions
{
    protected Options|null $options = null;

    protected int $columns = 0;

    /**
     * List of options to display in the field.
     */
    public function options(Options $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Adds a single option.
     */
    public function addOption(Option $option): static
    {
        if ($this->options === null) {
            $this->options = Options::make();
        }
        $this->options->add($option);

        return $this;
    }
}
