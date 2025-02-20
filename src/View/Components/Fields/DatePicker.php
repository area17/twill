<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;

class DatePicker extends TwillFormComponent
{
    public function __construct(
        string $name,
        string $label,
        bool $renderForBlocks = false,
        bool $renderForModal = false,
        bool $translated = false,
        bool $required = false,
        ?string $note = '',
        mixed $default = null,
        bool $disabled = false,
        bool $readOnly = false,
        bool $inModal = false,
        // Component specific
        public bool $withTime = true,
        public bool $allowInput = false,
        public bool $allowClear = false,
        public ?string $placeholder = null,
        public bool $timeOnly = false,
        public bool $time24Hr = false,
        public ?string $altFormat = null,
        public ?int $hourIncrement = null,
        public ?int $minuteIncrement = null
    ) {
        parent::__construct(
            name: $name,
            label: $label,
            note: $note,
            inModal: $inModal,
            readOnly: $readOnly,
            renderForBlocks: $renderForBlocks,
            renderForModal: $renderForModal,
            disabled: $disabled,
            required: $required,
            translated: $translated,
            default: $default
        );
    }

    public function render(): View
    {
        return view('twill::partials.form._date_picker', $this->data());
    }
}
