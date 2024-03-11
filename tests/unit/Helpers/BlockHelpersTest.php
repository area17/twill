<?php

namespace A17\Twill\Tests\Unit\Helpers;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Tests\Unit\TestCase;
use A17\Twill\View\Components\Blocks\TwillBlockComponent;
use Spatie\Once\Cache;

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
}

class AppBlock extends GroupBlock
{
    public static function getBlockGroup(): string
    {
        return 'app';
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

        $available = TwillBlocks::generateListOfAvailableBlocks()->pluck('componentClass')->filter();
        // Ensure correct order
        $this->assertEquals([AppBlock::class, GroupBlock::class, GroupBlock2::class], $available->all());

        $available = TwillBlocks::generateListOfAvailableBlocks([GroupBlock2::class, 'app-block'])->pluck('componentClass');
        // Ensure correct order
        $this->assertEquals([GroupBlock2::class, AppBlock::class], $available->all());

        $available = TwillBlocks::generateListOfAvailableBlocks([GroupBlock2::class, AppBlock::class], ['group', 'app'])->pluck('componentClass');
        $this->assertEquals([GroupBlock2::class, GroupBlock::class, AppBlock::class], $available->all());


        $available = TwillBlocks::generateListOfAvailableBlocks(['app-block', GroupBlock2::class], excludeBlocks: ['app-block'])->pluck('componentClass');
        $this->assertEquals([GroupBlock2::class], $available->all());

        $available = TwillBlocks::generateListOfAvailableBlocks(
            [AppBlock::class, GroupBlock::class, GroupBlock2::class],
            excludeBlocks: fn (Block $block) => $block->componentClass && $block->componentClass::getBlockName() == 'block'
        )->pluck('componentClass');

        $this->assertEquals([GroupBlock2::class], $available->all());

        TwillBlocks::globallyExcludeBlocks([GroupBlock::class]);
        TwillBlocks::globallyExcludeBlocks(fn (Block $block) => $block->name == 'group-block2');

        $available = TwillBlocks::generateListOfAvailableBlocks(
            excludeBlocks: fn (Block $block) => $block->source == Block::SOURCE_TWILL
        )->pluck('componentClass');
        $this->assertCount(1, $available);
        $this->assertContains(AppBlock::class, $available);


        $available = TwillBlocks::generateListOfAvailableBlocks(
            blocks: fn (Block $block) => $block->name == 'group-block2' ? true : ($block->source == Block::SOURCE_TWILL ? false : null)
        )->pluck('componentClass');
        $this->assertCount(2, $available);
        $this->assertEquals([AppBlock::class, GroupBlock2::class], $available->all());


        TwillBlocks::setGloballyExcludedBlocks();

        config(['twill.block_editor.block_rules.order' =>  ['group-block2', AppBlock::class, 'group-block']]);

        Cache::getInstance()->flush();

        $available = TwillBlocks::generateListOfAvailableBlocks()->pluck('componentClass')->filter();
        // Ensure correct order
        $this->assertEquals([GroupBlock2::class, AppBlock::class, GroupBlock::class], $available->all());

        config(['twill.block_editor.block_rules.disable' =>  ['group-block2', AppBlock::class]]);

        Cache::getInstance()->flush();

        $available = TwillBlocks::generateListOfAvailableBlocks()->pluck('componentClass')->filter();
        $this->assertEquals([GroupBlock::class], $available->all());

    }

}
