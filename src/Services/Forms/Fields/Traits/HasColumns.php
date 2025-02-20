<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait HasColumns
{
    protected int $columns = 0;

    /**
     * The amount of column to show the option in (when using unpack).
     */
    public function columns(int $columns = 1): static
    {
        $this->columns = $columns;

        return $this;
    }
}
