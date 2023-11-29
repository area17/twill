<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;

class Input extends TwillFormComponent
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
        public string $type = 'text',
        public ?string $placeholder = '',
        public ?string $direction = 'auto',
        public ?int $maxlength = null,
        public ?int $rows = null,
        public ?string $ref = null,
        public ?string $onChange = null,
        public ?string $onChangeAttribute = null,
        public ?string $prefix = null,
        public null|int|float $min = null,
        public null|int|float $max = null,
        public null|int|float $step = null,
        public ?string $mask = null,
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
        return view(
            'twill::partials.form._input',
            array_merge($this->data(), [
                'onChangeFullAttribute' => $this->onChangeAttribute ? "('" . $this->onChangeAttribute . "', ...arguments)" : "",
            ])
        );
    }
}
