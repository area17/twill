<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Fields\TimePicker;

class TimePickerFieldTest extends ComponentTestBase
{
    public string $component = TimePicker::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
    ];
    public string $expectedView = 'twill::partials.form._date_picker';
    public bool $noFieldTest = true;
}
