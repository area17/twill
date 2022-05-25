<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

use Illuminate\Support\Collection;

trait inlineable
{
    protected bool $inline = false;

    /**
     * Shows the option inline.
     */
    public function inline(bool $inline = true): self
    {
        $this->inline = $inline;

        return $this;
    }
}
