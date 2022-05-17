<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\hasBorder;
use A17\Twill\Services\Forms\Fields\Traits\hasOptions;
use A17\Twill\Services\Forms\Fields\Traits\inlineable;
use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;

class Radios extends BaseFormField
{
    use isTranslatable;
    use hasOptions;
    use inlineable;
    use hasBorder;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Radios::class,
            mandatoryProperties: ['name', 'label', 'options']
        );
    }
}
