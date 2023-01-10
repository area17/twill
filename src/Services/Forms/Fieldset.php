<?php

namespace A17\Twill\Services\Forms;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Fieldset
{
    protected function __construct(
        public ?string $title = null,
        public ?Collection $fields = null,
        public ?string $id = null,
        public ?bool $open = true,
    ) {
    }

    public static function make(): self
    {
        return new self();
    }

    /**
     * Set the title of the fieldset.
     */
    public function title(string $title): static
    {
        $this->title = $title;

        if (! $this->id) {
            $this->id(Str::slug($title));
        }

        return $this;
    }

    /**
     * Set the id of the field.
     */
    public function id(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Marks the fielset as open.
     */
    public function open(bool $open = true): self
    {
        $this->open = $open;

        return $this;
    }

    /**
     * Marks the fielset as closed.
     */
    public function closed(): self
    {
        $this->open = false;

        return $this;
    }

    /**
     * Set the form fields of the fieldset.
     */
    public function fields(array $fields): self
    {
        $this->fields = collect($fields);

        return $this;
    }
}
