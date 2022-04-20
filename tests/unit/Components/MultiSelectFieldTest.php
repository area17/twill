<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\MultiSelect;

class MultiSelectFieldTest extends ComponentWithOptionsTestBase
{
    public string $component = MultiSelect::class;
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

    public string $expectedView = 'twill::partials.form._multi_select';
}
