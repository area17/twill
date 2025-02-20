<?php

namespace A17\Twill\Tests\Unit\Components;


use A17\Twill\View\Components\Fields\Checkbox;

class CheckboxFieldTest extends ComponentTestBase
{
    public string $component = Checkbox::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
    ];
    public string $expectedView = 'twill::partials.form._checkbox';

    public string $field = \A17\Twill\Services\Forms\Fields\Checkbox::class;
    public array $fieldSetters = [
        'name' => 'name',
    ];
}
