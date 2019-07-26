<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\User;
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\Factory as ViewFactory;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
     */

    use ResetsPasswords;

    /**
     * @var Redirector
     */
    protected $redirector;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    public function __construct(Config $config, Redirector $redirector, ViewFactory $viewFactory)
    {
        parent::__construct();

        $this->redirector = $redirector;
        $this->viewFactory = $viewFactory;
        $this->config = $config;

        $this->redirectTo = $this->config->get('twill.auth_login_redirect_path', '/');
        $this->middleware('twill_guest');
    }

    /**
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard()
    {
        return Auth::guard('twill_users');
    }

    /**
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('twill_users');
    }

    /**
     * @param Request $request
     * @param string|null $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $user = $this->getUserFromToken($token);

        // call exists on the Password repository to check for token expiration (default 1 hour)
        // otherwise redirect to the ask reset link form with error message
        if ($user && Password::broker('twill_users')->getRepository()->exists($user, $token)) {
            return $this->viewFactory->make('twill::auth.passwords.reset')->with([
                'token' => $token,
                'email' => $user->email,
            ]);
        }

        return $this->redirector->to(route('admin.password.reset.link'))->withErrors([
            'token' => 'Your password reset token has expired or could not be found, please retry.',
        ]);
    }

    /**
     * @param Request $request
     * @param string|null $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showWelcomeForm(Request $request, $token = null)
    {
        $user = $this->getUserFromToken($token);

        // we don't call exists on the Password repository here because we don't want to expire the token for welcome emails
        if ($user) {
            return $this->viewFactory->make('twill::auth.passwords.reset')->with([
                'token' => $token,
                'email' => $user->email,
                'welcome' => true,
            ]);
        }

        return $this->redirector->to(route('admin.password.reset.link'))->withErrors([
            'token' => 'Your password reset token has expired or could not be found, please retry.',
        ]);
    }

    /**
     * Attempts to find a user with the given token.
     *
     * Since Laravel 5.4, reset tokens are encrypted, but we support both cases here
     * https://github.com/laravel/framework/pull/16850
     *
     * @param string $token
     * @return \A17\Twill\Models\User|null
     */
    private function getUserFromToken($token)
    {
        $clearToken = DB::table($this->config->get('auth.passwords.twill_users.table', 'twill_password_resets'))->where('token', $token)->first();

        if ($clearToken) {
            return User::where('email', $clearToken->email)->first();
        }

        foreach (DB::table($this->config->get('auth.passwords.twill_users.table', 'twill_password_resets'))->get() as $passwordReset) {
            if (Hash::check($token, $passwordReset->token)) {
                return User::where('email', $passwordReset->email)->first();
            }
        }

        return null;
    }
}
