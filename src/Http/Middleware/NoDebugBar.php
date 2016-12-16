<?php

namespace A17\CmsToolkit\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;

class NoDebugBar
{

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle($request, Closure $next)
    {
        if ($this->app->environment('development', 'local', 'staging')) {
            if (config('cms-toolkit.debug.debug_bar_in_fe')) {
                if (config('cms-toolkit.debug.use_inspector', false)) {
                    li()->turnOff();
                } else {
                    \Debugbar::disable();
                }
            }
        }

        return $next($request);
    }
}
