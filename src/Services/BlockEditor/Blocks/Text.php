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
        return view($this->view('title'), [
            'title' => $this->getInput('title'),
        ]);
    }

    public function blocktextToHtml()
    {
        return view($this->view('text'), [
            'text' => $this->getInput('html'),
        ]);
    }

    public function blockquoteToHtml()
    {
        return view($this->view('quote'), [
            'text' => $this->getInput('text'),
        ]);
    }
}
