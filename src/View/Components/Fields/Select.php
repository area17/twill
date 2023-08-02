<?php

namespace A17\Twill\View\Components\Fields;

use A17\Twill\Services\Forms\Options;
use Illuminate\Contracts\View\View;

class Select extends FieldWithOptions
{
    public function __construct(
        string $name,
        string $label,
        public bool $native = false,
        public bool $inTable = false,
        public bool $inGrid = false,
        bool $renderForBlocks = false,
        bool $renderForModal = false,
        bool $translated = false,
        bool $required = false,
        ?string $note = '',
        mixed $default = null,
        bool $disabled = false,
        bool $readOnly = false,
        bool $inModal = false,
        mixed $options = null,
        bool $unpack = false,
        int $columns = 0,
        bool $searchable = false,
        ?string $placeholder = '',
        bool $addNew = false,
        ?string $moduleName = null,
        ?string $storeUrl = null,
    ) {
        if ($options instanceof Options) {
            $options = $options->toArray();
        }

        parent::__construct(
            $name,
            $label,
            $renderForBlocks,
            $renderForModal,
            $translated,
            $required,
            $note,
            $default,
            $disabled,
            $readOnly,
            $inModal,
            $options,
            $unpack,
            $columns,
            $searchable,
            $placeholder,
            $addNew,
            $moduleName,
            $storeUrl,
        );
    }

    public function render(): View
    {
        return view('twill::partials.form._select', $this->data());
    }
}
