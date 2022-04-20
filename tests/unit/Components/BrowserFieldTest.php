<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Browser;

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
    public string $expectedView = 'twill::partials.form._browser';
}
