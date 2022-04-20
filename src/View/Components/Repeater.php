<?php

namespace A17\Twill\View\Components;

use Illuminate\View\View;

class Repeater extends TwillFormComponent
{
    public function __construct(
        // Component specific
        public string $type,
        public bool $buttonAsLink = false,
        public bool $reorder = true,
        public ?int $max = null,
        // Generic
        bool $renderForBlocks = false,
        bool $renderForModal = false,
        ?string $name = null,
    ) {
        parent::__construct(
            name: $name ?? $type,
            label: $name ?? $type,
            renderForBlocks: $renderForBlocks,
            renderForModal: $renderForModal,
        );
    }

    public function render(): View
    {
        return view('twill::partials.form._repeater');
    }
}
