<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\Services\Forms\Option;
use A17\Twill\Services\Forms\Options;
use A17\Twill\View\Components\Fields\Select;

class SelectFieldTest extends ComponentTestBase
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
    public string $field = \A17\Twill\Services\Forms\Fields\Select::class;
    public array $fieldSetters = [
        'name' => 'name',
    ];
    public string $expectedView = 'twill::partials.form._select';

    protected function setUp(): void
    {
        parent::setUp();

        $this->fieldSetters['options'] = new Options([
            new Option('key', 'value'),
        ]);
    }
}
