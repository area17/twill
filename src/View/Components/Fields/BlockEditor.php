<?php

namespace A17\Twill\View\Components\Fields;

use A17\Twill\Facades\TwillBlocks;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class BlockEditor extends TwillFormComponent
{
    public function __construct(
        string $name = 'default',
        ?string $label = null,
        bool $renderForBlocks = false,
        bool $renderForModal = false,
        // Component specific
        public array $blocks = [],
        public mixed $excludeBlocks = [],
        public array $groups = [],
        public bool $withoutSeparator = false,
        public ?string $group = null,
        public ?string $trigger = null,
        public bool $isSettings = false,
        public bool $usingDefaultOrder = false,
    ) {
        parent::__construct(
            name: $name,
            label: $label ?? Str::title($name),
            renderForBlocks: $renderForBlocks,
            renderForModal: $renderForModal,
        );

        $this->trigger = $trigger ?? $label ?? twillTrans('twill::lang.fields.block-editor.add-content');
    }

    public function getAllowedBlocks(): array
    {
        $groups = [];
        if ($this->group) {
            $groups = [$this->group];
        } elseif ($this->groups) {
            $groups = $this->groups;
        }

        return TwillBlocks::generateListOfAvailableBlocks(
            $this->blocks ?? null,
            $groups,
            $this->isSettings,
            $this->excludeBlocks ?? null,
            $this->usingDefaultOrder
        )->pluck('name')->all();
    }

    public function render(): View
    {
        return view(
            'twill::partials.form._block_editor',
            array_merge($this->data(), [
                'allowedBlocks' => $this->getAllowedBlocks(),
                'editorName' => [
                    'label' => $this->label,
                    'value' => $this->name,
                    'nested' => $this->renderForBlocks,
                ],
            ])
        );
    }
}
