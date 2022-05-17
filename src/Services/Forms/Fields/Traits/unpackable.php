<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

use Illuminate\Support\Collection;

trait unpackable
{
    protected bool $unpack = false;

    public function unpack(bool $unpack = true): self
    {
        $this->unpack = $unpack;

        return $this;
    }
}
