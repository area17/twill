<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class Impersonate
{
    public function __construct(protected AuthFactory $authFactory)
    {
    }

    /**
     * Handles an incoming request.
     *
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next)
    {
        if ($request->session()->has('impersonate')) {
            $this->authFactory->guard('twill_users')->onceUsingId($request->session()->get('impersonate'));
        }

        return $next($request);
    }
}
