<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\User;
use Auth;
use Crypt;
use Password;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    protected function guard()
    {
        return Auth::guard('twill_users');
    }

    public function showLoginForm()
    {
        return view('twill::auth.login');
    }

    public function showLogin2FaForm()
    {
        return view('twill::auth.2fa');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect(route('admin.login'));
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->google_2fa_secret && $user->google_2fa_enabled) {
            $this->guard()->logout();

            $request->session()->put('2fa:user:id', $user->id);

            return redirect(route('admin.login-2fa.form'));
        }

        $user->last_login_at = Carbon::now();
        $user->save();

        if ($user->require_new_password) {
            $this->logout($request);
            $token = Password::broker('twill_users')->getRepository()->create($user);
            return redirect(route('admin.password.reset.form', $token))->withErrors([
                'error' => 'Your password needs to be reset before login',
            ]);
        }

        return redirect()->intended($this->redirectTo);
    }

    public function login2Fa(Request $request)
    {
        $userId = $request->session()->get('2fa:user:id');

        $user = User::findOrFail($userId);

        $valid = (new Google2FA)->verifyKey(
            Crypt::decrypt($user->google_2fa_secret),
            $request->input('verify-code')
        );

        if ($valid) {
            Auth::guard('twill_users')->loginUsingId($userId);

            $request->session()->pull('2fa:user:id');

            return redirect()->intended($this->redirectTo);
        }

        return redirect(route('admin.login-2fa.form'))->withErrors([
            'error' => 'Your one time password is invalid.',
        ]);

    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('twill_guest', ['except' => 'logout']);
        $this->redirectTo = config('twill.auth_login_redirect_path', '/');
    }
}
