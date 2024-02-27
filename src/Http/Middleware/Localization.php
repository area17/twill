<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Localization
{
    /**
     * Handles an incoming request.
     *
     * @param  Request  $request
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
