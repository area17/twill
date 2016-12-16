<?php

namespace A17\CmsToolkit\Http\Middleware;

use Auth;
use Closure;

class Impersonate
{

    public function handle($request, Closure $next)
    {
        if ($request->session()->has('impersonate')) {
            Auth::onceUsingId($request->session()->get('impersonate'));
        }

        return $next($request);
    }
}
