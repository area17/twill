<?php

namespace A17\Twill\Http\Middleware;

use Auth;
use Closure;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = 'twill_users')
    {
        if (Auth::guard($guard)->check()) {
            return redirect(config('twill.auth_login_redirect_path', '/'));
        }

        return $next($request);
    }
}
