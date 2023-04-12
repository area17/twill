<?php

namespace A17\Twill\View\Components\Partials\Navigation;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumbs extends Component
{
    public function __construct(public array $breadcrumb = [])
    {
    }

    public function render(): View
    {
        return view(
            'twill::partials.navigation._breadcrumb',
        );
    }
}
