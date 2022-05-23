<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait unpackable
{
    protected bool $unpack = false;

    /**
     * Shows the option in a grid.
     */
    public function unpack(bool $unpack = true): self
    {
        $this->unpack = $unpack;

        return $this;
    }

    /**
     * The amount of column to show the option in (when using unpack).
     */
    public function columns(int $columns = 1): self
    {
        $this->columns = $columns;

        return $this;
    }
}
