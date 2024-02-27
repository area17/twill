<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;

class Impersonate
{
    /**
     * @var AuthFactory
     */
    protected $authFactory;

    public function __construct(AuthFactory $authFactory)
    {
        $this->authFactory = $authFactory;
    }

    /**
     * Handles an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->session()->has('impersonate')) {
            $this->authFactory->guard('twill_users')->onceUsingId($request->session()->get('impersonate'));
        }

        return $next($request);
    }
}
