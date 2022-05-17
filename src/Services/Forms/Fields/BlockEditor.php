<?php

namespace A17\Twill\Services\Forms\Fields;

class BlockEditor extends BaseFormField
{
    protected array $blocks = [];

    public static function make(): static
    {
        $field = new static(\A17\Twill\View\Components\BlockEditor::class);
        $field->name('block_editor');

        return $field;
    }

    public function blocks(array $blocks): self {
        $this->blocks = $blocks;

        return $this;
    }

}
