<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;

class RedirectIfAuthenticated
{
    /**
     * @var AuthManager
     */
    protected $authManager;

    /**
     * @param AuthManager $authManager
     */
    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    public function handle($request, Closure $next, $guard = 'twill_users')
    {
        if ($this->authManager->guard($guard)->check()) {
            return redirect(config('twill.auth_login_redirect_path', '/'));
        }

        return $next($request);
    }
}
