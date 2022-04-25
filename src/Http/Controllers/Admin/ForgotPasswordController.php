<?php

namespace A17\Twill\Http\Controllers\Admin;

use Illuminate\Auth\Passwords\PasswordBrokerManager;
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

    public function __construct(protected PasswordBrokerManager $passwordBrokerManager)
    {
        parent::__construct();
        $this->middleware('twill_guest');
    }

    public function broker(): \Illuminate\Contracts\Auth\PasswordBroker
    {
        return $this->passwordBrokerManager->broker('twill_users');
    }

    public function showLinkRequestForm(ViewFactory $viewFactory): \Illuminate\Contracts\View\View
    {
        return $viewFactory->make('twill::auth.passwords.email');
    }
}
