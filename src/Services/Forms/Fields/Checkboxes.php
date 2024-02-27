<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\HasBorder;
use A17\Twill\Services\Forms\Fields\Traits\HasColumns;
use A17\Twill\Services\Forms\Fields\Traits\HasMax;
use A17\Twill\Services\Forms\Fields\Traits\HasMin;
use A17\Twill\Services\Forms\Fields\Traits\HasOptions;
use A17\Twill\Services\Forms\Fields\Traits\Inlineable;
use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;

class Checkboxes extends BaseFormField
{
    use HasBorder;
    use HasColumns;
    use HasMax;
    use HasMin;
    use HasOptions;
    use Inlineable;
    use IsTranslatable;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Fields\Checkboxes::class,
            mandatoryProperties: ['name', 'label', 'options']
        );
    }
}
