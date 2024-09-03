<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\CanHaveButtonOnTop;
use A17\Twill\Services\Forms\Fields\Traits\HasFieldNote;
use A17\Twill\Services\Forms\Fields\Traits\HasMax;
use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;

class Assets extends BaseFormField
{
    use IsTranslatable;
    use HasMax;
    use HasFieldNote;
    use CanHaveButtonOnTop;

    public static function make(): static
    {
        $instance = new self(
            component: \A17\Twill\View\Components\Fields\Assets::class,
            mandatoryProperties: ['name', 'label']
        );

        // Max needs to be 1 by default for this component.
        // Cannot be null.
        $instance->max = 1;

        return $instance;
    }
}
