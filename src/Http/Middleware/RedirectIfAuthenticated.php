<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Routing\Redirector;

class RedirectIfAuthenticated
{
    public function __construct(protected AuthFactory $authFactory, protected Redirector $redirector, protected Config $config)
    {
    }

    /**
     * Handles an incoming request.
     *
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next, $guard = 'twill_users')
    {
        if ($this->authFactory->guard($guard)->check()) {
            return $this->redirector->to($this->config->get('twill.auth_login_redirect_path', '/'));
        }

        return $next($request);
    }
}
