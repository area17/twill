<?php

namespace A17\Twill\Facades;

use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Blocks\BlockCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void globallyExcludeBlocks(array|callable $blocks)
 * @method static array getGloballyExcludedBlocks
 * @method static void setGloballyExcludedBlocks(array $exclude = [])
 * @method static Collection<Block>getBlocks
 * @method static BlockCollection getBlockCollection
 * @method static Collection<Block>getSettingsBlocks
 * @method static Collection<Block>getRepeaters
 * @method static registerManualBlock(string $blockClass, string $source = Block::SOURCE_APP)
 * @method static Collection<Block>generateListOfAllBlocks(bool $settingsOnly = false)
 * @method static Collection<Block>getListOfUsedBlocks()
 * @method static Collection<Block>generateListOfAvailableBlocks(array|callable $blocks = null, ?array $groups = null, bool $settingsOnly = false, array|callable $excludeBlocks = null, bool $defaultOrder = false)
 */
class TwillBlocks extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \A17\Twill\TwillBlocks::class;
    }
}
