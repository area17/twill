<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\User;
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Carbon\Carbon;
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

    use ResetsPasswords {
        sendResetResponse as traitSendResetResponse;
    }

    /**
     * The path the user should be redirected to.
     *
     * @var string
     */
    protected $redirectTo;

    public function __construct(protected Config $config, protected Redirector $redirector, protected ViewFactory $viewFactory)
    {
        parent::__construct();

        $this->redirectTo = $this->config->get('twill.auth_login_redirect_path', '/');
        $this->middleware('twill_guest');
    }

    protected function guard(): \Illuminate\Contracts\Auth\Guard
    {
        return Auth::guard('twill_users');
    }

    public function broker(): \Illuminate\Contracts\Auth\PasswordBroker
    {
        return Password::broker('twill_users');
    }

    protected function sendResetResponse(Request $request, $response)
    {
        $user = User::where('email', $request->input('email'))->first();
        if (!$user->isActivated()) {
            $user->registered_at = Carbon::now();
            $user->save();
        }

        if ($user->require_new_password) {
            $user->require_new_password = false;
            $user->save();
        }

        return $this->traitSendResetResponse($request, $response);
    }

    /**
     * @param string|null $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
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

        return $this->redirector->to(route('twill.password.reset.link'))->withErrors([
            'token' => 'Your password reset token has expired or could not be found, please retry.',
        ]);
    }

    /**
     * @param string|null $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showWelcomeForm(Request $request, $token = null): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        $user = $this->getUserFromToken($token);

        // we don't call exists on the Password repository here because we don't want to expire the token for welcome emails
        if ($user !== null) {
            return $this->viewFactory->make('twill::auth.passwords.reset')->with([
                'token' => $token,
                'email' => $user->email,
                'welcome' => true,
            ]);
        }

        return $this->redirector->to(route('twill.password.reset.link'))->withErrors([
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
    private function getUserFromToken(string $token)
    {
        $clearToken = DB::table($this->config->get('auth.passwords.twill_users.table', 'twill_password_resets'))->where('token', $token)->first();

        if ($clearToken !== null) {
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
