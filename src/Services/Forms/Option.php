<?php

namespace A17\Twill\Services\Forms;

use Illuminate\Contracts\Support\Arrayable;

class Option implements Arrayable
{
    public function __construct(
        protected string|int $value,
        protected string $label,
    ) {
    }

    public static function make(string|int $value, string $label): self
    {
        return new self($value, $label);
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label,
        ];
    }
}
