<?php

namespace A17\Twill\View\Components;

use Illuminate\Contracts\View\View;

class Color extends TwillFormComponent
{
    public function render(): View
    {
        return view('twill::partials.form._color', $this->data());
    }
}
