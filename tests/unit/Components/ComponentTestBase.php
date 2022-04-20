<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\Tests\Unit\TestCase;
use A17\Twill\View\Components\TwillFormComponent;
use Illuminate\View\Component;

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
    }

    protected function getMakeComponent(): TwillFormComponent
    {
        return app()->make($this->component, $this->data);
    }
}
