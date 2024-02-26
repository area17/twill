<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\HasOptions;
use A17\Twill\Services\Forms\Fields\Traits\HasPlaceholder;
use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;
use A17\Twill\Services\Forms\Fields\Traits\Unpackable;

class Select extends BaseFormField
{
    use HasOptions;
    use HasPlaceholder;
    use IsTranslatable;
    use Unpackable;

    protected bool $searchable = false;

    public static function make(): static
    {
        return new self(
            component: \A17\Twill\View\Components\Fields\Select::class,
            mandatoryProperties: ['name', 'label', 'options']
        );
    }

    /**
     * If the options should be searchable.
     */
    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }
}
