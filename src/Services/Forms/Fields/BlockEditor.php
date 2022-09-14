<?php

namespace A17\Twill\Services\Forms\Fields;

class BlockEditor extends BaseFormField
{
    protected array $blocks = [];

    protected bool $isSettings = false;

    protected bool $withoutSeparator = false;

    public static function make(): static
    {
        $field = new self(\A17\Twill\View\Components\Fields\BlockEditor::class);
        $field->name('default');

        return $field;
    }

    public function name(string $name): static
    {
        if ($this->label === 'Default') {
            $this->label = null;
        }

        return parent::name($name);
    }

    public function withoutSeparator(bool $withoutSeparator = true): self
    {
        $this->withoutSeparator = $withoutSeparator;

        return $this;
    }

    public function isSettings(bool $isSettings = true): self
    {
        $this->isSettings = $isSettings;

        return $this;
    }

    /**
     * Default is all, but using this method you can limit the block types the field can use.
     */
    public function blocks(array $blocks): self
    {
        $this->blocks = $blocks;

        return $this;
    }
}
