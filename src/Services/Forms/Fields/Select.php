<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\HasOptions;
use A17\Twill\Services\Forms\Fields\Traits\HasPlaceholder;
use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;
use A17\Twill\Services\Forms\Fields\Traits\Unpackable;

class Select extends BaseFormField
{
    use IsTranslatable;
    use HasOptions;
    use HasPlaceholder;
    use Unpackable;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Fields\Select::class,
            mandatoryProperties: ['name', 'label', 'options']
        );
    }
}
