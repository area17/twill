<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Files;

class FilesFieldTest extends ComponentTestBase
{
    public string $component = Files::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
    ];
    public string $expectedView = 'twill::partials.form._files';
}
