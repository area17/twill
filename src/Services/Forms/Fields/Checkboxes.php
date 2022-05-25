<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\hasBorder;
use A17\Twill\Services\Forms\Fields\Traits\hasMax;
use A17\Twill\Services\Forms\Fields\Traits\hasMin;
use A17\Twill\Services\Forms\Fields\Traits\hasOptions;
use A17\Twill\Services\Forms\Fields\Traits\inlineable;
use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;

class Checkboxes extends BaseFormField
{
    use isTranslatable;
    use hasOptions;
    use hasMax;
    use hasMin;
    use inlineable;
    use hasBorder;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Checkboxes::class,
            mandatoryProperties: ['name', 'label', 'options']
        );
    }
}
