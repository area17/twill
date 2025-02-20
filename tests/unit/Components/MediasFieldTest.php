<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Fields\Medias;

class MediasFieldTest extends ComponentTestBase
{
    public string $component = Medias::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
    ];
    public string $expectedView = 'twill::partials.form._medias';

    public string $field = \A17\Twill\Services\Forms\Fields\Medias::class;
    public array $fieldSetters = [
        'name' => 'name',
    ];
}
