<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class Files extends TwillFormComponent
{
    public function __construct(
        string $name,
        string $label,
        bool $renderForBlocks = false,
        bool $renderForModal = false,
        string $note = null,
        // Component specific
        public int $max = 1,
        public int $filesizeMax = 0,
        public bool $buttonOnTop = false,
        public ?string $itemLabel = null,
        public ?string $fieldNote = null,
    ) {
        $itemLabel = $itemLabel ?? strtolower($label);
        $this->itemLabel = $itemLabel;

        parent::__construct(
            name: $name,
            label: $label,
            note: $note ?? 'Add' . ($max > 1 ? " up to $max $itemLabel" : ' one ' . Str::singular($itemLabel)),
            renderForBlocks: $renderForBlocks,
            renderForModal: $renderForModal
        );
    }

    public function render(): View
    {
        return view('twill::partials.form._files', $this->data());
    }
}
