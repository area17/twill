<?php

namespace A17\Docs;

use Illuminate\Support\Str;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

class BladeComponentStart implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        $currentLine = $cursor->getLine();

        if (!Str::startsWith($currentLine, ':::')) {
            return BlockStart::none();
        }

        $data = mb_strtolower(str_replace(' ', '-', trim($currentLine, ' :')));

        if (!$data) {
            return BlockStart::none();
        }

        $component = Str::before(Str::after($data, '#'), '=');
        $data = Str::after(Str::after($data, '#'), '=');

        $propsToAdd = [];
        $properties = explode('&', $data);

        foreach ($properties as $property) {
            $details = explode('.', $property);
            if (count($details) === 2) {
                if (Str::contains($details[1], '|')) {
                    $propsToAdd[$details[0]] = explode('|', $details[1]);
                } else {
                    $propsToAdd[$details[0]] = $details[1];
                }
            }
        }

        $cursor->advanceToEnd();

        return BlockStart::of(new BladeComponentParser($component, $propsToAdd))->at($cursor);
    }
}
