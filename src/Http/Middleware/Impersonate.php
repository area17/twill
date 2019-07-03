<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;

class Impersonate
{
    /**
     * @var AuthManager
     */
    protected $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    public function handle($request, Closure $next)
    {
        if ($request->session()->has('impersonate')) {
            $this->authManager->guard('twill_users')->onceUsingId($request->session()->get('impersonate'));
        }

        return $next($request);
    }
}
