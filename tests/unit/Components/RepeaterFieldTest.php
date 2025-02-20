<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Fields\Repeater;

class RepeaterFieldTest extends ComponentTestBase
{
    public string $component = Repeater::class;
    public array $data = [
        'type' => 'type',
    ];
    public string $expectedView = 'twill::partials.form._repeater';
    public string $field = \A17\Twill\Services\Forms\Fields\Repeater::class;
    public array $fieldSetters = [
        'type' => 'some_type',
    ];
}
