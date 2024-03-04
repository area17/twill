<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Blocks\Block;

class BlockEditor extends BaseFormField
{
    protected array $blocks = [];
    protected array $groups = [];

    protected mixed $excludeBlocks = [];

    protected bool $isSettings = false;

    protected bool $withoutSeparator = false;
    protected bool $usingDefaultOrder = false;

    public static function make(): static
    {
        $field = new self(\A17\Twill\View\Components\Fields\BlockEditor::class);
        $field->name('default');

        return $field;
    }

    public function name(string $name): static
    {
        if ($this->label === null) {
            $this->label = twillTrans('twill::lang.fields.block-editor.add-content');
        }

        return parent::name($name);
    }

    public function withoutSeparator(bool $withoutSeparator = true): static
    {
        $this->withoutSeparator = $withoutSeparator;

        return $this;
    }

    public function isSettings(bool $isSettings = true): static
    {
        $this->isSettings = $isSettings;

        return $this;
    }

    /**
     * Default is all, but using this method you can limit the block types the field can use.
     */
    public function blocks(array $blocks): static
    {
        // For backward compatibility, clear the list of excludeBlocks in case both ->excludeBlocks()->blocks() were called
        $this->excludeBlocks = [];
        $this->blocks = $blocks;

        return $this;
    }


    public function usingDefaultOrder(bool $usingDefaultOrder = true): static
    {
        $this->usingDefaultOrder = $usingDefaultOrder;

        return $this;
    }

    public function getBlocks(): array
    {
        return $this->blocks;
    }

    public function groups(array $groups)
    {
        $this->groups = $groups;

        return $this;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * Use this method if you want to exclude any block types
     *
     * @param array<string>|callable<Block> $blocks
     */
    public function excludeBlocks(array|callable $blocks): static
    {
        $this->excludeBlocks = $blocks;

        return $this;
    }
}
