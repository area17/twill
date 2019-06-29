<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
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

    /**
     * @var AuthManager
     */
    protected $authManager;

    /**
     * @var Encrypter
     */
    protected $encrypter;

    /**
     * @var Redirector
     */
    protected $redirector;

    /**
     * Create a new controller instance.
     *
     * @param AuthManager $authManager
     * @param Encrypter $encrypter
     * @param Redirector $redirector
     * @return void
     */
    public function __construct(AuthManager $authManager, Encrypter $encrypter, Redirector $redirector)
    {
        parent::__construct();

        $this->authManager = $authManager;
        $this->encrypter = $encrypter;
        $this->redirector = $redirector;

        $this->middleware('twill_guest', ['except' => 'logout']);
        $this->redirectTo = config('twill.auth_login_redirect_path', '/');
    }

    protected function guard()
    {
        return $this->authManager->guard('twill_users');
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

        return $this->redirector->to(route('admin.login'));
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->google_2fa_secret && $user->google_2fa_enabled) {
            $this->guard()->logout();

            $request->session()->put('2fa:user:id', $user->id);

            return $this->redirector->to(route('admin.login-2fa.form'));
        }

        return $this->redirector->intended($this->redirectTo);
    }

    public function login2Fa(Request $request)
    {
        $userId = $request->session()->get('2fa:user:id');

        $user = User::findOrFail($userId);

        $valid = (new Google2FA)->verifyKey(
            $this->encrypter->decrypt($user->google_2fa_secret),
            $request->input('verify-code')
        );

        if ($valid) {
            $this->authManager->guard('twill_users')->loginUsingId($userId);

            $request->session()->pull('2fa:user:id');

            return $this->redirector->intended($this->redirectTo);
        }

        return $this->redirector->to(route('admin.login-2fa.form'))->withErrors([
            'error' => 'Your one time password is invalid.',
        ]);

    }
}
