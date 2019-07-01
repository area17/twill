<?php

namespace A17\Twill\Http\Controllers\Admin;

use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
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

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * Create a new controller instance.
     *
     * @param Application $app
     * @param PasswordBrokerManager $passwordBrokerManager
     * @param ViewFactory $viewFactory
     * @return void
     */
    public function __construct(
        Application $app,
        PasswordBrokerManager $passwordBrokerManager,
        ViewFactory $viewFactory
    ) {
        parent::__construct($app);

        $this->passwordBrokerManager = $passwordBrokerManager;
        $this->viewFactory = $viewFactory;
        $this->middleware('twill_guest');
    }

    /**
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return $this->passwordBrokerManager->broker('twill_users');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return $this->viewFactory->make('twill::auth.passwords.email');
    }
}
