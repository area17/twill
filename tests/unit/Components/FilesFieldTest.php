<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Fields\Files;

class FilesFieldTest extends ComponentTestBase
{
    public string $component = Files::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
    ];
    public string $expectedView = 'twill::partials.form._files';

    public string $field = \A17\Twill\Services\Forms\Fields\Files::class;
    public array $fieldSetters = [
        'name' => 'name',
    ];
}
