<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Medias;

class MediasFieldTest extends ComponentTestBase
{
    public string $component = Medias::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
    ];
    public string $expectedView = 'twill::partials.form._medias';
}
