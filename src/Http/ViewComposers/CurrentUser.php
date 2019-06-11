<?php

namespace A17\Twill\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class CurrentUser
{
    /**
     * Binds data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $currentUser = auth('twill_users')->user();

        $view->with(compact('currentUser'));
    }
}
