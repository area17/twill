<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;

class Map extends TwillFormComponent
{
    public function __construct(
        string $name,
        string $label,
        bool $renderForBlocks = false,
        bool $renderForModal = false,
        ?string $note = '',
        bool $inModal = false,
        // Component specific
        public bool $showMap = true,
        public bool $openMap = false,
        public bool $saveExtendedData = false,
        public bool $autoDetectLatLngValue = false
    ) {
        parent::__construct(
            name: $name,
            label: $label,
            note: $note,
            inModal: $inModal,
            renderForBlocks: $renderForBlocks,
            renderForModal: $renderForModal
        );
    }

    public function render(): View
    {
        return view('twill::partials.form._map', $this->data());
    }
}
