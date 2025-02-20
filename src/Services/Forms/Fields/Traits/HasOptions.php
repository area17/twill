<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

use A17\Twill\Services\Forms\Option;
use A17\Twill\Services\Forms\Options;
use Closure;

trait HasOptions
{
    protected Closure|Options|null $options = null;

    protected int $columns = 0;

    /**
     * List of options to display in the field.
     */
    public function options(Options|Closure|array $options): static
    {
        $this->options = is_array($options)
            ? Options::fromArray($options)
            : $options;

        return $this;
    }

    /**
     * Adds a single option.
     */
    public function addOption(Option $option): static
    {
        if ($this->options instanceof Closure) {
            throw new \Exception('addOption cannot be used with an options closure.');
        }

        if ($this->options === null) {
            $this->options = Options::make();
        }
        $this->options->add($option);

        return $this;
    }

    protected function getOptions(): Options
    {
        if ($this->options instanceof Closure) {
            $execute = $this->options;
            return $execute();
        }

        return $this->options;
    }
}
