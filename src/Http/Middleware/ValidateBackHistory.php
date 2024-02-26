<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateBackHistory
{
    /**
     * Handles an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        return method_exists($response, 'header') ? $response
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT') : $response;
    }
}
