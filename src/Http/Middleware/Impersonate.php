<?php

namespace A17\Twill\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class Impersonate
{

    public function handle($request, Closure $next)
    {
        if ($request->session()->has('impersonate')) {
            Auth::guard('twill_users')->onceUsingId($request->session()->get('impersonate'));
        }

        return $next($request);
    }
}
