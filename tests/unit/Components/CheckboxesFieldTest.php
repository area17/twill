<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Checkboxes;

class CheckboxesFieldTest extends ComponentWithOptionsTestBase
{
    public string $component = Checkboxes::class;
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
    public string $expectedView = 'twill::partials.form._checkboxes';
}
