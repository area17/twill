<?php

namespace A17\Twill\Http\ViewComposers;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\View\View;

class CurrentUser
{
    public function __construct(protected AuthFactory $authFactory)
    {
    }

    /**
     * Binds data to the view.
     */
    public function compose(View $view): void
    {
        $currentUser = $this->authFactory->guard('twill_users')->user();

        $view->with(['currentUser' => $currentUser]);
    }
}
