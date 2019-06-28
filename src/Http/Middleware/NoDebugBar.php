<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use Barryvdh\Debugbar\LaravelDebugbar;

class NoDebugBar
{
    protected $app;

    /**
     * @var LaravelDebugbar
     */
    protected $debugbar;

    /**
     * @param Application $app
     * @param LaravelDebugbar $debugbar
     */
    public function __construct(Application $app, LaravelDebugbar $debugbar)
    {
        $this->app = $app;
        $this->debugbar = $debugbar;
    }

    public function handle($request, Closure $next)
    {
        if ($this->app->environment('development', 'local', 'staging')) {
            if (!config('twill.debug.debug_bar_in_fe')) {
                if (config('twill.debug.use_inspector', false)) {
                    li()->turnOff();
                } else {
                    $this->debugbar->disable();
                }
            }
        }

        return $next($request);
    }
}
