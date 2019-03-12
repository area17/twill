<?php

namespace Sb4yd3e\Twill\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class CurrentUser
{
    public function compose(View $view)
    {
        $currentUser = auth('twill_users')->user();

        $view->with(compact('currentUser'));
    }
}
