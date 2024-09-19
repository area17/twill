<?php

namespace A17\Twill\Services\Forms\Fields;

class Assets extends Medias
{
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
