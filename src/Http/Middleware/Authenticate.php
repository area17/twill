<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class Authenticate extends Middleware
{
    /**
     * {@inheritDoc}
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        if (
            (
                !$request->user() ||
                !$request->user()->published
            ) && $request->route()?->getName() !== 'twill.login.form'
        ) {
            Auth::logout();
            return $request->expectsJson()
                ? abort(403, 'Your account is not verified.')
                : Redirect::guest(URL::route(config('twill.admin_route_name_prefix') . 'login.form'));
        }

        return $next($request);
    }

    /**
     * {@inheritDoc}
     */
    protected function redirectTo($request)
    {
        return route(config('twill.admin_route_name_prefix') . 'login.form');
    }
}
