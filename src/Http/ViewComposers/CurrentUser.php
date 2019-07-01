<?php

namespace A17\Twill\Http\ViewComposers;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\View\View;

class CurrentUser
{
    /**
     * @var AuthFactory
     */
    protected $authFactory;

    /**
     * @param AuthFactory $authFactory
     */
    public function __construct(AuthFactory $authFactory)
    {
        $this->authFactory = $authFactory;
    }

    public function compose(View $view)
    {
        $currentUser = $this->authFactory->guard('twill_users')->user();

        $view->with(compact('currentUser'));
    }
}
