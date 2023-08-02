<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Fields\Input;

class InputFieldTest extends ComponentTestBase
{
    public string $component = Input::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
    ];
    public string $expectedView = 'twill::partials.form._input';

    public string $field = \A17\Twill\Services\Forms\Fields\Input::class;
    public array $fieldSetters = [
        'name' => 'name',
    ];
}
