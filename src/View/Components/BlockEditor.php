<?php

namespace A17\Twill\View\Components;

use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;

class BlockEditor extends TwillFormComponent
{
    public function __construct(
        string $name = 'default',
        ?string $label = null,
        bool $renderForBlocks = false,
        bool $renderForModal = false,
        // Component specific
        public array $blocks = [],
        public array $groups = [],
        public bool $withoutSeparator = false,
        public ?string $group = null,
        public ?string $trigger = null,
    ) {
        parent::__construct(
            name: $name,
            label: $label ?? Str::title($name),
            renderForBlocks: $renderForBlocks,
            renderForModal: $renderForModal,
        );
        $this->trigger = $trigger ?? $label ?? twillTrans('twill::lang.fields.block-editor.add-content');
    }

    public function render(): View
    {
        $groups = [];
        if ($this->group) {
            $groups = [$this->group];
        } elseif ($this->groups) {
            $groups = $this->groups;
        }
        return view('twill::partials.form._block_editor', [
            ... $this->data(),
            'allowedBlocks' => generate_list_of_available_blocks(
                $this->blocks ?? null,
                $groups
            ),
            'editorName' => [
                'label' => $this->label,
                'value' => $this->name,
            ],
        ]);
    }
}
