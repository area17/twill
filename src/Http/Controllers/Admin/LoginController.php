<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Http\Requests\Admin\OauthRequest;
use A17\Twill\Models\User;
use A17\Twill\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository as Config;
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Password;
use Illuminate\View\Factory as ViewFactory;
use Laravel\Socialite\Facades\Socialite;
use PragmaRX\Google2FA\Google2FA;

/**
 * |--------------------------------------------------------------------------
 * | Login Controller
 * |--------------------------------------------------------------------------
 * |
 * | This controller handles authenticating users for the application and
 * | redirecting them to your home screen. The controller uses a trait
 * | to conveniently provide its functionality to your applications.
 * |
 */
class LoginController extends Controller
{
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
     * @var Config
     */
    protected $config;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * The path the user should be redirected to.
     *
     * @var string
     */
    protected $redirectTo;

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
        $this->config = $config;

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
        if ($this->autologin()) {
            return redirect()->back();
        }

        return $this->viewFactory->make('twill::auth.login');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function showLogin2FaForm()
    {
        return $this->viewFactory->make('twill::auth.2fa');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (config('twill.dashboard.auth_activity_log.logout', false)) {
            activity()->performedOn($this->guard()->user())->causedBy($this->guard()->user())->log('logout');
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->redirector->to(route(config('twill.admin_route_name_prefix') . 'login'));
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        return $this->afterAuthentication($request, $user);
    }

    protected function afterAuthentication(Request $request, $user): RedirectResponse
    {
        if ($user->google_2fa_secret && $user->google_2fa_enabled) {
            $this->guard()->logout();

            $request->session()->put('2fa:user:id', $user->id);

            return $this->redirector->to(route(config('twill.admin_route_name_prefix') . 'login-2fa.form'));
        }

        $user->last_login_at = Carbon::now();
        $user->save();

        if ($user->require_new_password) {
            $this->logout($request);
            $token = Password::broker('twill_users')->getRepository()->create($user);

            return $this->redirector->to(route(config('twill.admin_route_name_prefix') . 'password.reset.form', $token))->withErrors([
                'error' => 'Your password needs to be reset before login',
            ]);
        }

        if (config('twill.dashboard.auth_activity_log.login', false)) {
            activity()->performedOn($user)->causedBy($user)->log('login');
        }

        return $this->redirector->intended($this->redirectTo);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
     */
    public function login2Fa(Request $request)
    {
        $userId = $request->session()->get('2fa:user:id');

        $user = twillModel('user')::findOrFail($userId);

        $valid = (new Google2FA())->verifyKey(
            $user->google_2fa_secret,
            $request->input('verify-code')
        );

        if ($valid) {
            $this->authManager->guard('twill_users')->loginUsingId($userId);

            $request->session()->pull('2fa:user:id');

            return $this->redirector->intended($this->redirectTo);
        }

        return $this->redirector->to(route(config('twill.admin_route_name_prefix') . 'login-2fa.form'))->withErrors([
            'error' => 'Your one time password is invalid.',
        ]);
    }

    /**
     * @param string $provider Socialite provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider, OauthRequest $request)
    {
        return Socialite::driver($provider)
            ->scopes($this->config->get('twill.oauth.' . $provider . '.scopes', []))
            ->with($this->config->get('twill.oauth.' . $provider . '.with', []))
            ->redirect();
    }

    /**
     * @param string $provider Socialite provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider, OauthRequest $request)
    {
        $oauthUser = Socialite::driver($provider)->user();
        $repository = App::make(UserRepository::class);

        // If the user with that email exists
        if ($user = $repository->oauthUser($oauthUser)) {
            // If that provider has been linked
            if ($repository->oauthIsUserLinked($oauthUser, $provider)) {
                $user = $repository->oauthUpdateProvider($oauthUser, $provider);
                // Login and redirect
                $this->authManager->guard('twill_users')->login($user);
                return $this->afterAuthentication($request, $user);
            } elseif ($user->password) {
                // If the user has a password then redirect to a form to ask for it
                // before linking a provider to that email
                $request->session()->put('oauth:user_id', $user->id);
                $request->session()->put('oauth:user', $oauthUser);
                $request->session()->put('oauth:provider', $provider);
                return $this->redirector->to(route(config('twill.admin_route_name_prefix') . 'login.oauth.showPasswordForm'));
            } else {
                $user->linkProvider($oauthUser, $provider);

                // Login and redirect
                $this->authManager->guard('twill_users')->login($user);

                return $this->afterAuthentication($request, $user);
            }
        } else {
            // If the user doesn't exist, create it
            $user = $repository->oauthCreateUser($oauthUser);
            $user->linkProvider($oauthUser, $provider);

            // Login and redirect
            $this->authManager->guard('twill_users')->login($user);

            return $this->redirector->intended($this->redirectTo);
        }
    }

    /**
     * @return \Illuminate\View\View
     */
    public function showPasswordForm(Request $request)
    {
        $userId = $request->session()->get('oauth:user_id');
        $user = twillModel('user')::findOrFail($userId);

        return $this->viewFactory->make('twill::auth.oauth-link', [
            'username' => $user->email,
            'provider' => $request->session()->get('oauth:provider'),
        ]);
    }

    /**
     * @param string $provider Socialite provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function linkProvider(Request $request)
    {
        // If provided credentials are correct
        if ($this->attemptLogin($request)) {
            // Load the user
            $userId = $request->session()->get('oauth:user_id');
            $user = twillModel('user')::findOrFail($userId);

            // Link the provider and login
            $user->linkProvider($request->session()->get('oauth:user'), $request->session()->get('oauth:provider'));
            $this->authManager->guard('twill_users')->login($user);

            // Remove session variables
            $request->session()->forget('oauth:user_id');
            $request->session()->forget('oauth:user');
            $request->session()->forget('oauth:provider');

            // Login and redirect
            return $this->afterAuthentication($request, $user);
        } else {
            return $this->sendFailedLoginResponse($request);
        }
    }

    /**
     * This method checks to make sure the user is published.
     */
    protected function credentials(Request $request): array
    {
        return array_merge($request->only($this->username(), 'password'), ['published' => 1]);
    }

    protected function autologin(): bool
    {
        if (!$this->autologinEnabled()) {
            return false;
        }

        return $this->guard()->attempt([
            $this->username() => $this->config->get('twill.autologin.email'),
            'password' => $this->config->get('twill.autologin.password'),
        ], false);
    }

    protected function autologinEnabled(): bool
    {
        if (!$this->config->get('twill.autologin.enabled', false)) {
            return false;
        }

        if (blank($this->config->get('twill.autologin.email'))) {
            return false;
        }

        if (blank($this->config->get('twill.autologin.password'))) {
            return false;
        }

        if (blank($environments = $this->config->get('twill.autologin.environments', []))) {
            return false;
        }

        if (!in_array(app()->environment(), $environments)) {
            return false;
        }

        return true;
    }
}
