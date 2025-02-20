<?php

namespace A17\Twill\Services\Forms;

use Illuminate\Contracts\Support\Arrayable;

class Option implements Arrayable
{
    public function __construct(
        protected string|int $value,
        protected string $label,
        protected bool $selectable = true,
    ) {
    }

    public static function make(string|int $value, string $label, bool $selectable = true): self
    {
        return new self($value, $label, $selectable);
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label,
            'selectable' => $this->selectable,
        ];
    }
}
