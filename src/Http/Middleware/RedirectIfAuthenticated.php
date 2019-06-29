<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
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
     * @param AuthManager $authManager
     * @param Redirector $redirector
     */
    public function __construct(AuthManager $authManager, Redirector $redirector)
    {
        $this->authManager = $authManager;
        $this->redirector = $redirector;
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
            return $this->redirector->to(config('twill.auth_login_redirect_path', '/'));
        }

        return $next($request);
    }
}
