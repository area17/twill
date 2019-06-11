<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;

class NoDebugBar
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handles an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->app->environment('development', 'local', 'staging')) {
            if (!config('twill.debug.debug_bar_in_fe')) {
                if (config('twill.debug.use_inspector', false)) {
                    li()->turnOff();
                } else {
                    \Debugbar::disable();
                }
            }
        }

        return $next($request);
    }
}
