<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;

class MultiSelect extends FieldWithOptions
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
        // FieldWithOptions
        mixed $options = null,
        bool $unpack = false,
        int $columns = 0,
        bool $searchable = false,
        ?string $placeholder = null,
        bool $addNew = false,
        ?string $moduleName = null,
        ?string $storeUrl = null,
        // Component specific
        public ?int $min = null,
        public ?int $max = null,
        public ?string $endpoint = null,
        public bool $taggable = false,
        public bool $pushTags = false
    ) {
        parent::__construct(
            name: $name,
            label: $label,
            renderForBlocks: $renderForBlocks,
            renderForModal: $renderForModal,
            translated: $translated,
            required: $required,
            note: $note,
            default: $default,
            disabled: $disabled,
            readOnly: $readOnly,
            inModal: $inModal,
            options: $options,
            unpack: $unpack,
            columns: $columns,
            searchable: $searchable,
            placeholder: $placeholder,
            addNew: $addNew,
            moduleName: $moduleName,
            storeUrl: $storeUrl,
        );
    }

    public function render(): View
    {
        return view('twill::partials.form._multi_select', $this->data());
    }
}
