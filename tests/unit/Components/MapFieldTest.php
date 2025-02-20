<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Fields\Map;

class MapFieldTest extends ComponentTestBase
{
    public string $component = Map::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
    ];
    public string $expectedView = 'twill::partials.form._map';

    public string $field = \A17\Twill\Services\Forms\Fields\Map::class;
    public array $fieldSetters = [
        'name' => 'name',
    ];
}
