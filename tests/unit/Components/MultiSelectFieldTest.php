<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\Services\Forms\Option;
use A17\Twill\Services\Forms\Options;
use A17\Twill\View\Components\Fields\MultiSelect;

class MultiSelectFieldTest extends ComponentTestBase
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
    public string $field = \A17\Twill\Services\Forms\Fields\MultiSelect::class;
    public array $fieldSetters = [
        'name' => 'name',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->fieldSetters['options'] = new Options([
            new Option('key', 'value'),
        ]);
    }
}
