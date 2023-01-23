<?php

namespace A17\Twill\Services\Forms\Fields\Helpers;

use Illuminate\Support\Str;

class TiptapWrapper
{
    public const ELEMENT_UL = 'ul';
    public const ELEMENT_OL = 'ol';
    public const ELEMENT_NONE = null;

    private const SUPPORTED_ELEMENTS = ['ul', 'ol', null];

    public function __construct(
        public string $className,
        public ?string $label = null,
        public ?string $icon = null,
        public ?string $createElement = self::ELEMENT_NONE
    ) {
        if (! in_array($this->createElement, self::SUPPORTED_ELEMENTS, true)) {
            throw new \Exception($this->createElement . ' is not supported in tiptapWrappers');
        }

        if (! $this->label) {
            $this->label = $this->className;
        }
    }

    public function toArray(): array
    {
        return [
            'id' => Str::slug($this->className),
            'label' => $this->label,
            'className' => $this->className,
            'createElement' => $this->createElement,
            'icon' => $this->icon,
        ];
    }
}
