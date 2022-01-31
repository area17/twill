<?php

namespace A17\Twill\View\Components;

class Repeater extends TwillFormComponent
{
    public $buttonAsLink;
    public $type;

    public function __construct(
        $type,
        $name = null,
        $buttonAsLink = false
    )
    {
        parent::__construct($name ?? $type, $name);

        $this->buttonAsLink = $buttonAsLink;
        $this->type = $type;
    }

    public function render()
    {
        return view('twill::partials.form._repeater');
    }
}
