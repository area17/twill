<?php

namespace A17\CmsToolkit\Http\Middleware;

use Auth;
use Closure;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect(config('cms-toolkit.auth_login_redirect_path', '/home'));
        }

        return $next($request);
    }
}
