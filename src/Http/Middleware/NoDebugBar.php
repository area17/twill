<?php

namespace A17\Twill\Http\Middleware;

use Barryvdh\Debugbar\LaravelDebugbar;
use Closure;
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application;

class NoDebugBar
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var LaravelDebugbar
     */
    protected $debugbar;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Application $app, LaravelDebugbar $debugbar, Config $config)
    {
        $this->app = $app;
        $this->debugbar = $debugbar;
        $this->config = $config;
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
            if (!$this->config->get('twill.debug.debug_bar_in_fe')) {
                if ($this->config->get('twill.debug.use_inspector', false)) {
                    li()->turnOff();
                } else {
                    $this->debugbar->disable();
                }
            }
        }

        return $next($request);
    }
}
