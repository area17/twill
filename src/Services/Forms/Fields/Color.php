<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;

class Color extends BaseFormField
{
    use IsTranslatable;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Fields\Color::class,
            mandatoryProperties: ['name', 'label']
        );
    }
}
