<?php

namespace A17\Twill\Tests\Unit\Components;

use A17\Twill\Exceptions\MissingRequiredComponentData;

abstract class ComponentWithOptionsTestBase extends ComponentTestBase
{
    public function testExceptionWhenNoOptionsSet(): void
    {
        $this->data = ['name' => 'name', 'label' => 'label'];

        $this->expectException(MissingRequiredComponentData::class);
        $this->expectExceptionMessage('options');

        $this->getMakeComponent();
    }
}
