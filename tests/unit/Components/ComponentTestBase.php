<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\Tests\Unit\TestCase;
use A17\Twill\View\Components\Fields\TwillFormComponent;

abstract class ComponentTestBase extends TestCase
{
    /**
     * The component class.
     */
    public string $component;

    public bool $noFieldTest = false;

    /**
     * The field class.
     */
    public string $field;

    /**
     * The setters to test.
     */
    public array $fieldSetters = [];

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

        // Trigger the actual render.
        $rendered->render();
    }

    public function testFieldClass(): void
    {
        if ($this->noFieldTest) {
            $this->assertTrue(true);
            return;
        }
        /** @var \A17\Twill\Services\Forms\Fields\BaseFormField $class */
        $class = $this->field::make();

        foreach ($this->fieldSetters as $method => $value) {
            // Value may be null so we do not pass anything and leave it to the default.
            if ($value) {
                $class->{$method}($value);
            } else {
                $class->{$method}();
            }
        }

        $this->assertEquals($this->expectedView, $class->render()->name());

        // Trigger the actual render.
        $class->render()->render();
    }

    protected function getMakeComponent(): TwillFormComponent
    {
        return app()->make($this->component, $this->data);
    }
}
