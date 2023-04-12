<?php

namespace A17\Twill\Tests\Unit\Components;


use A17\Twill\View\Components\Fields\Browser;

class BrowserFieldTest extends ComponentTestBase
{
    public string $component = Browser::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
        'endpoints' => [
            [
                'label' => 'Demo',
                'value' => '/bar/foo',
            ],
        ],
    ];
    public string $field = \A17\Twill\Services\Forms\Fields\Browser::class;
    public array $fieldSetters = [
        'name' => 'browser_name',
        'endpoints' => [
            [
                'label' => 'Demo',
                'value' => '/bar/foo',
            ],
        ],
    ];
    public string $expectedView = 'twill::partials.form._browser';
}
