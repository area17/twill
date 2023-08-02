<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait HasOnChange
{
    protected ?string $ref = null;

    protected ?string $onChange = null;

    protected ?string $onChangeAttribute = null;

    /**
     * The field to act on.
     */
    public function ref(string $ref): static
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Javascript to execute on change.
     */
    public function onChange(string $onChange): static
    {
        $this->onChange = $onChange;

        return $this;
    }

    /**
     * Attribute to change.
     */
    public function onChangeAttribute(string $onChangeAttribute): static
    {
        $this->onChangeAttribute = $onChangeAttribute;

        return $this;
    }
}
