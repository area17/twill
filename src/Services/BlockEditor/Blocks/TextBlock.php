<?php

namespace A17\CmsToolkit\Services\BlockEditor\Blocks;

class TextBlock extends BaseBlock
{
    protected $types = [
        'blocktext',
        'blockquote',
        'blocktitle',
    ];

    public function blocktextToHtml()
    {
        return view('front.blocks.text', [
            'text' => $this->data['html_' . $this->locale] ?? '',
        ]);
    }

    public function blockquoteToHtml()
    {
        return view('front.blocks.quote', [
            'text' => $this->data['text_' . $this->locale] ?? '',
        ]);
    }

    public function blocktitleToHtml()
    {
        return view('front.blocks.title', [
            'title' => $this->data['title_' . $this->locale] ?? '',
        ]);
    }
}
