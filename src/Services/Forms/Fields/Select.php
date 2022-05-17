<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\hasOptions;
use A17\Twill\Services\Forms\Fields\Traits\hasPlaceholder;
use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;

class Select extends BaseFormField
{
    use isTranslatable;
    use hasOptions;
    use hasPlaceholder;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Select::class,
            mandatoryProperties: ['name', 'label', 'options']
        );
    }
}
