<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;

class Wysiwyg extends TwillFormComponent
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
        public bool $hideCounter = false,
        public ?string $placeholder = null,
        public ?string $direction = 'auto',
        public bool $editSource = false,
        public ?array $toolbarOptions = null,
        public ?int $maxlength = null,
        public ?array $options = null,
        public string $type = 'tiptap',
        public bool $limitHeight = false,
        public bool $syntax = false,
        public string $customTheme = 'github',
        public ?array $customOptions = null,
        public ?array $browserModules = null,
        public ?array $endpoints = null,
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

        if (! $this->endpoints) {
            $this->endpoints = isset($this->browserModules) ? collect($this->browserModules)->map(function ($module) {
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
        }
    }

    public function render(): View
    {
        if ($this->toolbarOptions) {
            $toolbarOptions = array_map(static function ($option) {
                if ($option === 'list-unordered') {
                    return (object)['list' => 'bullet'];
                }

                if ($option === 'list-ordered') {
                    return (object)['list' => 'ordered'];
                }

                if ($option === 'h1') {
                    return (object)['header' => 1];
                }

                if ($option === 'h2') {
                    return (object)['header' => 2];
                }

                return $option;
            }, $this->toolbarOptions);

            $toolbarOptions = [
                'modules' => [
                    'toolbar' => $toolbarOptions,
                    'syntax' => $this->syntax,
                ],
            ];
        }

        $this->options = $this->customOptions ?? $toolbarOptions ?? null;

        return view(
            'twill::partials.form._wysiwyg',
            array_merge($this->data(), [
                'theme' => $this->customTheme,
                'activeSyntax' => $this->syntax
            ])
        );
    }
}
