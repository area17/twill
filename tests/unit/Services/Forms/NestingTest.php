<?php

namespace A17\Twill\Tests\Unit\Services\Forms;

use A17\Twill\Services\Forms\Columns;
use A17\Twill\Services\Forms\Fieldset;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Services\Forms\InlineRepeater;
use A17\Twill\Tests\Unit\TestCase;
use A17\Twill\TwillBlocks;

class NestingTest extends TestCase
{
    public function testRegisterDynamicRepeaters()
    {
        TwillBlocks::$dynamicRepeaters = [];
        $form = Form::make([
            Columns::make()->middle([
                InlineRepeater::make()->name('repeater1')
            ]),
            InlineRepeater::make()->name('repeater2'),
        ])->addFieldset(
            Fieldset::make()->fields([
                Columns::make()->left([
                    InlineRepeater::make()->name('set-repeater1')->fields([
                        InlineRepeater::make()->name('nested1')
                    ])
                ])->right([
                    InlineRepeater::make()->name('set-repeater2')
                ])
            ])
        );
        $form->registerDynamicRepeaters();
        $this->assertArrayHasKey('repeater1', TwillBlocks::$dynamicRepeaters);
        $this->assertArrayHasKey('repeater2', TwillBlocks::$dynamicRepeaters);
        $this->assertArrayHasKey('set-repeater1', TwillBlocks::$dynamicRepeaters);
        $this->assertArrayHasKey('nested1', TwillBlocks::$dynamicRepeaters);
        $this->assertArrayHasKey('set-repeater2', TwillBlocks::$dynamicRepeaters);
    }
}
