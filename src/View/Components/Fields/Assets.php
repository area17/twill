<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;

class Assets extends TwillFormComponent
{
    public function __construct(
        string $name,
        string $label,
        ?string $note = '',
        bool $renderForBlocks = false,
        bool $translated = false,
        public int $max = 1,
        public ?string $fieldNote = null,
        public bool $buttonOnTop = false,
    ) {
        parent::__construct(
            name: $name,
            label: $label,
            note: $note,
            renderForBlocks: $renderForBlocks,
            translated: $translated,
        );
    }

    public function render(): View
    {
        return view(
            'twill::partials.form._assets',
            $this->data()
        );
    }
}
