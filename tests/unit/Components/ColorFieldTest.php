<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Color;

class ColorFieldTest extends ComponentTestBase
{
    public string $component = Color::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
    ];
    public string $expectedView = 'twill::partials.form._color';
}
