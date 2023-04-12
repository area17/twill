<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\View\Components\Fields\Wysiwyg;

class WysiwygFieldTest extends ComponentTestBase
{
    public string $component = Wysiwyg::class;
    public array $data = [
        'name' => 'name',
        'label' => 'label',
    ];
    public string $expectedView = 'twill::partials.form._wysiwyg';
    public string $field = \A17\Twill\Services\Forms\Fields\Wysiwyg::class;
    public array $fieldSetters = [
        'name' => 'name',
    ];

    public function testTipTap(): void {
        $this->data['type'] = 'tiptap';

        $component = $this->getMakeComponent();

        $this->assertEquals($this->component, $component::class);

        /** @var \Illuminate\View\View $rendered */
        $rendered = $component->render();

        $this->assertEquals($rendered->getName(), $this->expectedView);
        $this->assertEquals('tiptap', $this->data['type']);
    }
}
