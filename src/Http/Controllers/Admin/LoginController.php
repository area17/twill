<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\User;
use A17\Twill\Http\Requests\Admin\OauthRequest;
use A17\Twill\Repositories\UserRepository;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository as Config;
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\Factory as ViewFactory;
use PragmaRX\Google2FA\Google2FA;
use Socialite;

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
     * @var ViewFactory
     */
    protected $viewFactory;

    public function __construct(
        Config $config,
        AuthManager $authManager,
        Encrypter $encrypter,
        Redirector $redirector,
        ViewFactory $viewFactory
    ) {
        parent::__construct();

        $this->authManager = $authManager;
        $this->encrypter = $encrypter;
        $this->redirector = $redirector;
        $this->viewFactory = $viewFactory;

        $this->middleware('twill_guest', ['except' => 'logout']);
        $this->redirectTo = $config->get('twill.auth_login_redirect_path', '/');
    }

    /**
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard()
    {
        return $this->authManager->guard('twill_users');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return $this->viewFactory->make('twill::auth.login');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function showLogin2FaForm()
    {
        return $this->viewFactory->make('twill::auth.2fa');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return $this->redirector->to(route('admin.login'));
    }

    /**
     * @param Request $request
     * @param \Illuminate\Foundation\Auth\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->google_2fa_secret && $user->google_2fa_enabled) {
            $this->guard()->logout();

            $request->session()->put('2fa:user:id', $user->id);

            return $this->redirector->to(route('admin.login-2fa.form'));
        }

        return $this->redirector->intended($this->redirectTo);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
     */
    public function login2Fa(Request $request)
    {
        $userId = $request->session()->get('2fa:user:id');

        $user = User::findOrFail($userId);

        $valid = (new Google2FA)->verifyKey(
            $user->google_2fa_secret,
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

    /**
     * @param string $provider Socialite provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider, OauthRequest $request)
    {

        return Socialite::driver($provider)->redirect();

    }

    /**
     * @param string $provider Socialite provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider, OauthRequest $request)
    {

        $oauthUser = Socialite::driver($provider)->user();
        $repository = app(UserRepository::class);

        // If the user with that email exists
        if ($user = $repository->oauthUser($oauthUser)) {

            // If that provider has been linked
            if ($repository->oauthIsUserLinked($oauthUser, $provider)) {
                $user = $repository->oauthUpdateProvider($oauthUser, $provider);

                // Login and redirect
                $this->authManager->guard('twill_users')->login($user);
                return $this->redirector->intended($this->redirectTo);
            } else {
                $user->LinkProvider($oauthUser, $provider);

                // Login and redirect
                $this->authManager->guard('twill_users')->login($user);
                return  $this->redirector->intended($this->redirectTo);

                // TODO: IMPLEMENT THIS FLOW
                // if ($user->password) {
                //     // If the user has a password then redirect to a form to ask for it
                //     // before linking an oauth account to that email

                //     // $request->session()->put('oauth:user_db', $user);
                //     // $request->session()->put('oauth:user', $oauthUser);
                //     // return $this->redirector->to(route('admin.login.requestPassword'));
                // } else {
                //     $user->LinkProvider($oauthUser, $provider);

                //     // Login and redirect
                //     $this->authManager->guard('twill_users')->login($user);
                //     return  $this->redirector->intended($this->redirectTo);
                // }
            }
        } else {
            // If the user doesn't exist, create it
            $user = $repository->oauthCreateUser($oauthUser);
            $user->LinkProvider($oauthUser, $provider);

            // Login and redirect
            $this->authManager->guard('twill_users')->login($user);
            return $this->redirector->intended($this->redirectTo);
        }

    }

}
