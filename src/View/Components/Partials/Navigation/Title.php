<?php

namespace A17\Twill\View\Components\Partials\Navigation;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Title extends Component
{
    public function render(): View
    {
        return view('twill::partials.navigation._title');
    }
}
