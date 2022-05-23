<?php

namespace A17\Twill\Http\Middleware;

use Closure;

class Localization
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
        if ($lang = $request->user()->language) {
            config(['twill.locale' => $lang]);
        }

        return $next($request);
    }
}
