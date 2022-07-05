<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait hasOnChange
{
    protected ?string $ref = null;

    protected ?string $onChange = null;

    protected ?string $onChangeAttribute = null;

    /**
     * The field to act on.
     */
    public function ref(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Javascript to execute on change.
     */
    public function onChange(string $onChange): self
    {
        $this->onChange = $onChange;

        return $this;
    }

    /**
     * Attribute to change.
     */
    public function onChangeAttribute(string $onChangeAttribute): self
    {
        $this->onChangeAttribute = $onChangeAttribute;

        return $this;
    }

}
