<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\User;
use DB;
use Hash;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Password;

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

    public function showResetForm(Request $request, $token = null)
    {
        $user = $this->getUserFromToken($token);

        // call exists on the Password repository to check for token expiration (default 1 hour)
        // otherwise redirect to the ask reset link form with error message
        if ($user && Password::getRepository()->exists($user, $token)) {
            return view('twill::auth.passwords.reset')->with([
                'token' => $token,
                'email' => $user->email,
            ]);
        }

        return redirect(route('admin.password.reset.link'))->withErrors([
            'token' => 'Your password reset token has expired or could not be found, please retry.',
        ]);
    }

    public function showWelcomeForm(Request $request, $token = null)
    {
        $user = $this->getUserFromToken($token);

        // we don't call exists on the Password repository here because we don't want to expire the token for welcome emails
        if ($user) {
            return view('twill::auth.passwords.reset')->with([
                'token'   => $token,
                'email'   => $user->email,
                'welcome' => true,
            ]);
        }

        return redirect(route('admin.password.reset.link'))->withErrors([
            'token' => 'Your password reset token has expired or could not be found, please retry.',
        ]);
    }

    /*
     * Since Laravel 5.4, reset tokens are encrypted, but we support both cases here
     * https://github.com/laravel/framework/pull/16850
     */
    private function getUserFromToken($token)
    {
        $clearToken = DB::table('password_resets')->where('token', $token)->first();

        if ($clearToken) {
            return User::where('email', $clearToken->email)->first();
        }

        foreach (DB::table('password_resets')->get() as $passwordReset) {
            if (Hash::check($token, $passwordReset->token)) {
                return User::where('email', $passwordReset->email)->first();
            }
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = config('twill.auth_login_redirect_path', '/home');
        $this->middleware('guest');
    }
}
