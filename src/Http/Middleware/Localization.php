<?php

namespace A17\Twill\Http\Middleware;

use Closure;

class Localization
{
    /**
     * Handles an incoming request.
     *
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next)
    {
        if ($request->user()->language) {
            config(['twill.locale' => $request->user()->language]);
        }

        return $next($request);
    }
}
