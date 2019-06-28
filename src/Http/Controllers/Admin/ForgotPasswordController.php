<?php

namespace A17\Twill\Http\Controllers\Admin;

use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

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

    protected $passwordBrokerManager;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PasswordBrokerManager $passwordBrokerManager)
    {
        parent::__construct();

        $this->passwordBrokerManager = $passwordBrokerManager;
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
        return view('twill::auth.passwords.email');
    }
}
