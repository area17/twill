<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Fields\DatePicker;

class DatePickerFieldTest extends ComponentTestBase
{
    public string $component = DatePicker::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
    ];
    public string $expectedView = 'twill::partials.form._date_picker';

    public string $field = \A17\Twill\Services\Forms\Fields\DatePicker::class;
    public array $fieldSetters = [
        'name' => 'name',
    ];
}
