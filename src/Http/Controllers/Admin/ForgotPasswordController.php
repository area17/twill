<?php

namespace A17\Twill\Http\Controllers\Admin;

use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\View\Factory as ViewFactory;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
     */

    use SendsPasswordResetEmails;

    /**
     * @var PasswordBrokerManager
     */
    protected $passwordBrokerManager;

    public function __construct(
        Application $app,
        Config $config,
        PasswordBrokerManager $passwordBrokerManager
    ) {
        parent::__construct($app, $config);

        $this->passwordBrokerManager = $passwordBrokerManager;
        $this->middleware('twill_guest');
    }

    public function broker()
    {
        return $this->passwordBrokerManager->broker('twill_users');
    }

    /**
     * @param ViewFactory $viewFactory
     * @return \Illuminate\Contracts\View\View
     */
    public function showLinkRequestForm(ViewFactory $viewFactory)
    {
        return $viewFactory->make('twill::auth.passwords.email');
    }
}
