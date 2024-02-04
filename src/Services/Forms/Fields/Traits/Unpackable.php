<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait Unpackable
{
    use HasColumns;

    protected bool $unpack = false;

    /**
     * Shows the option in a grid.
     */
    public function unpack(bool $unpack = true): static
    {
        $this->unpack = $unpack;

        return $this;
    }
}
