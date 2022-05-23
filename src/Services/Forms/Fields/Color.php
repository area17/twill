<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;

class Color extends BaseFormField
{
    use isTranslatable;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Color::class,
            mandatoryProperties: ['name', 'label']
        );
    }
}
