<?php

namespace A17\Twill\Http\Middleware;

use Closure;

class ValidateBackHistory
{
    /**
     * Handles an incoming request.
     *
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next)
    {
        $response = $next($request);

        return method_exists($response, 'header') ? $response
            ->header('Cache-Control', 'nocache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT') : $response;
    }
}
