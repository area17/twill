<?php

namespace A17\Twill\Services\Forms\Fields;

class BlockEditor extends BaseFormField
{
    protected array $blocks = [];

    public static function make(): static
    {
        $field = new self(\A17\Twill\View\Components\Fields\BlockEditor::class);
        $field->name('default');

        return $field;
    }

    public function name(string $name): BaseFormField
    {
        if ($this->label === 'Default') {
            $this->label = null;
        }

        return parent::name($name);
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
