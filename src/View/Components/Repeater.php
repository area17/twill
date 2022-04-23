<?php

namespace A17\Twill\View\Components;

use A17\Twill\Facades\TwillBlocks;
use Illuminate\Contracts\View\View;

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
        return view('twill::partials.form._repeater', $this->data());
    }

    public function repeaterForm(int $index, array $fields): string {
        $consumedData = [
            'repeaterKey' => 'form.repeaters.' . $this->type . '.' . $index,
        ];

        $repeater = TwillBlocks::findRepeaterByName($this->type);
        return $repeater->render($consumedData);
    }
}
