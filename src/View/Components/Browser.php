<?php

namespace A17\Twill\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class Browser extends TwillFormComponent
{
    public function __construct(
        string $label,
        bool $renderForBlocks = false,
        bool $renderForModal = false,
        bool $translated = false,
        bool $required = false,
        string $note = '',
        mixed $default = null,
        bool $disabled = false,
        bool $readOnly = false,
        bool $inModal = false,
        ?string $name = null,
        // Component specific
        public ?string $moduleName = null,
        public array $modules = [],
        public array $endpoints = [],
        public ?string $endpoint = null,
        public int $max = 1,
        public ?string $fieldNote = null,
        public ?string $browserNote = null,
        public ?string $itemLabel = null,
        public bool $buttonOnTop = false,
        public bool $wide = false,
        public bool $sortable = true,
        public ?string $routePrefix = null,
        public array $params = [],
        public ?string $connectedBrowserField = null,
    ) {
        $name = $name ?? $moduleName;
        parent::__construct(
            name: $name ?? $moduleName,
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

        $endpointsFromModules = isset($this->modules) ? collect($this->modules)->map(function ($module) {
            return [
                'label' => $module['label'] ?? ucfirst($module['name']),
                'value' => moduleRoute(
                    $module['name'],
                    $module['routePrefix'] ?? null,
                    'browser',
                    $module['params'] ?? [],
                    false
                ),
            ];
        })->toArray() : null;

        $this->endpoints = $this->endpoints ?? $endpointsFromModules ?? [];
        $this->endpoint = $this->endpoint ?? (!empty($endpoints) ? null : moduleRoute(
                $this->moduleName,
                $this->routePrefix,
                'browser',
                $this->params,
                false
            ));

        $this->itemLabel = $this->itemLabel ?? strtolower($this->label);

        $this->note = $this->note ??
            'Add' . ($this->max > 1 ? " up to {$this->max} " . $itemLabel : ' one ' . Str::singular($this->itemLabel));
    }

    public function render(): View
    {
        return view('twill::partials.form._browser', $this->data());
    }
}
