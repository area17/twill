<?php

namespace A17\Twill\Tests\Unit\Components;


use A17\Twill\View\Components\Fields\Color;

class ColorFieldTest extends ComponentTestBase
{
    public string $component = Color::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
    ];
    public string $expectedView = 'twill::partials.form._color';

    public string $field = \A17\Twill\Services\Forms\Fields\Color::class;
    public array $fieldSetters = [
        'name' => 'name',
    ];
}
