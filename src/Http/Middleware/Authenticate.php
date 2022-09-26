<?php

namespace A17\Twill\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class Authenticate extends Middleware
{

    /**
     * @param $request
     * @param \Closure $next
     * @param ...$guards
     * @return mixed
     */
    public function handle($request, $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        if (
            (
                !$request->user() ||
                !$request->user()->published
            ) && $request->route()->getName() !== 'twill.login.form'
        ) {
            Auth::logout();
            return $request->expectsJson()
                ? abort(403, 'Your account is not verified.')
                : Redirect::guest(URL::route('admin.login.form'));
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        return route('admin.login.form');
    }
}
