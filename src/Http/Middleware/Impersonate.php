<?php

namespace A17\Twill\Http\Middleware;

use Auth;
use Closure;

class Impersonate
{
    /**
     * Handles an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->session()->has('impersonate')) {
            Auth::guard('twill_users')->onceUsingId($request->session()->get('impersonate'));
        }

        return $next($request);
    }
}
