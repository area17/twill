<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

use A17\Twill\Services\Forms\Option;
use A17\Twill\Services\Forms\Options;

trait hasOptions
{
    protected Options|null $options = null;
    protected int $columns = 0;

    public function options(Options $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function addOption(Option $option): self {
        $this->options->add($option);

        return $this;
    }

    public function columns(int $columns = 1): self {
        $this->columns = $columns;

        return $this;
    }
}
