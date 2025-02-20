<?php

namespace A17\Docs;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

class BladeComponentParser extends AbstractBlockContinueParser
{
    private BladeComponentElement $block;

    public function __construct(string $className, array $properties)
    {
        $this->block = new BladeComponentElement($className, $properties);
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return true;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        $currentLine = $cursor->getLine();

        if ($currentLine === ':::#' . $this->block->getElement() . ':::') {
            return BlockContinue::finished();
        }

        return BlockContinue::at($cursor);
    }
}
