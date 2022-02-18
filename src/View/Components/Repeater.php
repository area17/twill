<?php

namespace A17\Twill\View\Components;

class Repeater extends TwillFormComponent
{
    public $buttonAsLink;
    public $type;
    public $max;

    public function __construct(
        $type,
        $name = null,
        $buttonAsLink = false,
        $renderForBlocks = false,
        $renderForModal = false,
        $max = null
    )
    {
        parent::__construct($name ?? $type, $name, $renderForBlocks, $renderForModal);

        $this->buttonAsLink = $buttonAsLink;
        $this->type = $type;
        $this->max = $max;
    }

    public function render()
    {
        return view('twill::partials.form._repeater');
    }
}
