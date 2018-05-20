<?php

namespace A17\Twill\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class CurrentUser
{
    public function compose(View $view)
    {
        $currentUser = auth()->user();

        $view->with(compact('currentUser'));
    }
}
