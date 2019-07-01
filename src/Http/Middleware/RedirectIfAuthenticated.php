<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository as Config;
use Illuminate\Routing\Redirector;

class RedirectIfAuthenticated
{
    /**
     * @var AuthManager
     */
    protected $authManager;

    /**
     * @var Redirector
     */
    protected $redirector;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param AuthManager $authManager
     * @param Redirector $redirector
     * @param Config $config
     */
    public function __construct(AuthManager $authManager, Redirector $redirector, Config $config)
    {
        $this->authManager = $authManager;
        $this->redirector = $redirector;
        $this->config = $config;
    }

    /**
     * Handles an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'twill_users')
    {
        if ($this->authManager->guard($guard)->check()) {
            return $this->redirector->to($this->config->get('twill.auth_login_redirect_path', '/'));
        }

        return $next($request);
    }
}
