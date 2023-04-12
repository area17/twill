<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\HasBorder;
use A17\Twill\Services\Forms\Fields\Traits\HasOptions;
use A17\Twill\Services\Forms\Fields\Traits\Inlineable;
use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;

class Radios extends BaseFormField
{
    use IsTranslatable;
    use HasOptions;
    use Inlineable;
    use HasBorder;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Fields\Radios::class,
            mandatoryProperties: ['name', 'label', 'options']
        );
    }
}
