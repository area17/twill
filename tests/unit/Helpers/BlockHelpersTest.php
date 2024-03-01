<?php

namespace A17\Twill\Tests\Unit\Helpers;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Tests\Unit\TestCase;
use A17\Twill\View\Components\Blocks\TwillBlockComponent;

class GroupBlock extends TwillBlockComponent {
    public static function getBlockGroup(): string
    {
        return 'group';
    }

    public static function getBlockName(): string
    {
        return 'block';
    }

    public function render()
    {
    }

    public function getForm(): Form
    {
        return new Form();
    }
}

class GroupBlock2 extends GroupBlock
{
    public static function getBlockName(): string
    {
        return 'block2';
    }

    public static function getPosition(): float|int|string
    {
        return 2;
    }
}

class AppBlock extends GroupBlock
{
    public static function getBlockGroup(): string
    {
        return 'app';
    }

    public static function getPosition(): float|int|string
    {
        return 1;
    }
}
class BlockHelpersTest extends TestCase
{
    public function testGenerateListOfAvailableBlocks()
    {
        TwillBlocks::registerManualBlock(GroupBlock::class);
        TwillBlocks::registerManualBlock(GroupBlock2::class, Block::SOURCE_CUSTOM);
        TwillBlocks::registerManualBlock(AppBlock::class);

        $blockClasses = collect(TwillBlocks::getBlocks())->pluck('componentClass')->all();
        $this->assertContains(GroupBlock::class, $blockClasses);
        $this->assertContains(AppBlock::class, $blockClasses);

        $available = TwillBlocks::generateListOfAvailableBlocks()->pluck('componentClass');

        $this->assertGreaterThanOrEqual(3, count($available));

        // Ensure correct order

        $this->assertEquals(GroupBlock2::class, $available->pop());
        $this->assertEquals(AppBlock::class, $available->pop());

        $available = TwillBlocks::generateListOfAvailableBlocks(['app-block', GroupBlock2::class])->pluck('componentClass');
        $this->assertCount(2, $available);
        $this->assertContains(AppBlock::class, $available);
        $this->assertContains(GroupBlock2::class, $available);

        $available = TwillBlocks::generateListOfAvailableBlocks([GroupBlock2::class], ['group'])->pluck('componentClass');
        $this->assertCount(2, $available);
        $this->assertContains(GroupBlock::class, $available);

        $available = TwillBlocks::generateListOfAvailableBlocks(['app-block', GroupBlock2::class], excludeBlocks: ['app-block'])->pluck('componentClass');

        $this->assertCount(1, $available);
        $this->assertContains(GroupBlock2::class, $available);

        $available = TwillBlocks::generateListOfAvailableBlocks(
            [AppBlock::class, GroupBlock::class, GroupBlock2::class],
            excludeBlocks: fn (Block $block) => $block->componentClass && $block->componentClass::getBlockName() == 'block'
        )->pluck('componentClass');

        $this->assertCount(1, $available);
        $this->assertContains(GroupBlock2::class, $available);

        TwillBlocks::globallyExcludeBlocks([GroupBlock::class]);
        TwillBlocks::globallyExcludeBlocks(fn (Block $block) => $block->name == 'group-block2');

        $available = TwillBlocks::generateListOfAvailableBlocks(
            excludeBlocks: fn (Block $block) => $block->source == Block::SOURCE_TWILL
        )->pluck('componentClass');
        $this->assertCount(1, $available);
        $this->assertContains(AppBlock::class, $available);
    }

}
