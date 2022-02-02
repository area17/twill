<?php

namespace A17\Twill\View\Components;

use Illuminate\Support\Str;

class BlockEditor extends TwillFormComponent
{
    public $blocks = [];
    public $groups = [];
    public $group;
    public $allowedBlocks;
    public $title;
    public $trigger;
    public $withoutSeparator;

    public function __construct(
        $label = null,
        $name = 'default',
        $renderForBlocks = false,
        $renderForModal = false,
        $trigger = null,
        $title = null,
        $blocks = [],
        $groups = [],
        $group = null,
        $withoutSeparator = false
    ) {
        parent::__construct($name, $label, $renderForBlocks, $renderForModal);
        $this->trigger = $trigger ?? $label ?? twillTrans('twill::lang.fields.block-editor.add-content');
        $this->blocks = $blocks;
        $this->groups = $groups;
        $this->group = $group;
        $this->allowedBlocks = generate_list_of_available_blocks(
            $this->blocks ?? null,
            $this->group ?? $this->groups ?? null
        );
        $this->title = $title ?? Str::title($name);
        $this->withoutSeparator = $withoutSeparator;
    }

    public function render()
    {
        return view('twill::partials.form._block_editor', [
            'editorName' => [
                'label' => $this->title,
                'value' => $this->name,
            ],
        ]);
    }
}
