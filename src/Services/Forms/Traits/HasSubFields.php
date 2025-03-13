<?php

namespace A17\Twill\Services\Forms\Traits;

use A17\Twill\Services\Forms\Contracts\CanHaveSubfields;
use Illuminate\Support\Collection;

trait HasSubFields
{
    public function registerDynamicRepeaters(): void
    {
        if ($this instanceof Collection) {
            $this->registerDynamicRepeatersFor($this);
        }
    }

    protected function registerDynamicRepeatersFor(?iterable $fields): void
    {
        if ($fields) {
            foreach ($fields as $field) {
                if ($field instanceof CanHaveSubfields) {
                    $field->registerDynamicRepeaters();
                }
            }
        }
    }
}
