<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;

class Repeater extends TwillFormComponent
{
    public function __construct(
        // Component specific
        public string $type,
        public bool $buttonAsLink = false,
        public bool $reorder = true,
        public ?int $max = null,
        public bool $allowCreate = true,
        public ?string $relation = null,
        public ?array $browserModule = null,
        // Generic
        bool $renderForBlocks = false,
        bool $renderForModal = false,
        ?string $label = null,
        ?string $name = null,
    ) {
        parent::__construct(
            name: $name ?? $type,
            label: $label ?? $type,
            renderForBlocks: $renderForBlocks,
            renderForModal: $renderForModal,
        );

        $this->browserModule = $browserModule ? [
            'label' => $browserModule['label'] ?? ucfirst($browserModule['name']),
            'value' => moduleRoute($browserModule['name'], $browserModule['routePrefix'] ?? null, 'browser', $browserModule['params'] ?? [], false)
        ] : null;
    }

    public function render(): View
    {
        return view('twill::partials.form._repeater', $this->data());
    }
}
