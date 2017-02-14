<?php

namespace A17\CmsToolkit\Services\BlockEditor\Blocks;

class Text extends BaseBlock
{
    protected $types = [
        'blocktitle',
        'blocktext',
        'blockquote',
    ];

    public function blocktitleToHtml()
    {
        return view('cms-toolkit::blocks.title', [
            'title' => $this->getInput('title'),
        ]);
    }

    public function blocktextToHtml()
    {
        return view('cms-toolkit::blocks.text', [
            'text' => $this->getInput('html'),
        ]);
    }

    public function blockquoteToHtml()
    {
        return view('cms-toolkit::blocks.quote', [
            'text' => $this->getInput('text'),
        ]);
    }
}
