<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\Tests\Unit\TestCase;
use A17\Twill\View\Components\TwillFormComponent;

abstract class ComponentTestBase extends TestCase
{
    /**
     * The component class.
     */
    public string $component;

    /**
     * The expected view name.
     */
    public string $expectedView;

    /**
     * The data to pass to the component.
     */
    public array $data = [];

    public function testRenderComponentWithData(): void
    {
        $component = $this->getMakeComponent();

        $this->assertEquals($this->component, $component::class);

        /** @var \Illuminate\View\View $rendered */
        $rendered = $component->render();

        $this->assertEquals($rendered->getName(), $this->expectedView);
        $this->assertArrayHasKey('name', $rendered->getData());
        $this->assertArrayHasKey('label', $rendered->getData());
    }

    protected function getMakeComponent(): TwillFormComponent
    {
        return app()->make($this->component, $this->data);
    }
}
