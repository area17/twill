<?php

namespace A17\Twill\View\Components\Partials\Navigation;

use A17\Twill\Facades\TwillNavigation;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tertiary extends Component
{
    /**
     * @return array<int, \A17\Twill\View\Components\Navigation\NavigationLink>
     */
    public function getLinks(): array
    {
        return TwillNavigation::getActiveSecondaryNavigationLink()?->getChildren() ?? [];
    }

    public function render(): View
    {
        return view('twill::partials.navigation._tertiary_navigation', ['links' => $this->getLinks()]);
    }
}
