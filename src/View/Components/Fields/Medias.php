<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;

class Medias extends TwillFormComponent
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
        public int $max = 1,
        public ?string $fieldNote = null,
        public bool $withAddInfo = true,
        public bool $withVideoUrl = true,
        public bool $withCaption = true,
        public ?int $altTextMaxLength = null,
        public ?int $captionMaxLength = null,
        public array $extraMetadatas = [],
        public int $widthMin = 0,
        public int $heightMin = 0,
        public bool $buttonOnTop = false,
        public bool $activeCrop = true
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
            'twill::partials.form._medias',
            array_merge(
                $this->data(),
                [
                    'multiple' => $this->max > 1 || $this->max === 0,
                ]
            )
        );
    }
}
