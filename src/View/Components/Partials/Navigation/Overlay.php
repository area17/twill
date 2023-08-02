<?php

namespace A17\Twill\View\Components\Partials\Navigation;

use A17\Twill\Facades\TwillNavigation;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Overlay extends Component
{
    public function render(): View
    {
        return view(
            'twill::partials.navigation._overlay_navigation',
            [
                'linkGroups' => TwillNavigation::buildNavigationTree(),
                'active_title' => TwillNavigation::getActivePrimaryNavigationLink()?->getTitle(),
                'search' => config('twill.enabled.search', true),
            ]
        );
    }
}
