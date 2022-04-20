<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Radios;

class RadiosFieldTest extends ComponentWithOptionsTestBase
{
    public string $component = Radios::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
        'options' => [
            [
                'label' => 'Bar',
                'value' => 'foo',
            ],
        ],
    ];

    public string $expectedView = 'twill::partials.form._radios';
}
