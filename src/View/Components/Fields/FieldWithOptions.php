<?php

namespace A17\Twill\View\Components\Fields;

use A17\Twill\Services\Forms\Options;

abstract class FieldWithOptions extends TwillFormComponent
{
    /** Below are unused but needed to keep compatible  */
    public ?string $confirmMessageText;

    public ?string $confirmTitleText;

    public ?bool $requireConfirmation;

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
        // Component specific.
        public mixed $options = null,
        public bool $unpack = false,
        public int $columns = 0,
        public bool $searchable = false,
        public ?string $placeholder = null,
        public bool $addNew = false,
        public ?string $moduleName = null,
        public ?string $storeUrl = null
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

        $this->options = $this->getOptions();

        $this->confirmMessageText = null;
        $this->confirmTitleText = null;
        $this->requireConfirmation = null;
    }

    public function getOptions(): array
    {
        if (null === $this->options) {
            $this->options = [];
        }

        if ($this->options instanceof Options) {
            return $this->options->toArray();
        }

        return is_object($this->options) && method_exists($this->options, 'map') ? $this->options->map(
            function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            }
        )->values()->toArray() : $this->options;
    }
}
