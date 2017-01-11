<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use DB;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

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
        return view('cms-toolkit::auth.passwords.reset')->with([
            'token' => $token,
            'email' => DB::table('password_resets')->where('token', $token)->first()->email,
        ]);
    }

    public function showWelcomeForm(Request $request, $token = null)
    {
        return view('cms-toolkit::auth.passwords.reset')->with([
            'token' => $token,
            'email' => DB::table('password_resets')->where('token', $token)->first()->email,
            'welcome' => true,
        ]);
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = config('cms-toolkit.auth.login_redirect_path', '/home');
        $this->middleware('guest');
    }
}
