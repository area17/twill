<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Select;

class SelectFieldTest extends ComponentWithOptionsTestBase
{
    public string $component = Select::class;
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

    public string $expectedView = 'twill::partials.form._select';
}
