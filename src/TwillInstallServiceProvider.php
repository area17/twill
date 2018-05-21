<?php

namespace A17\Twill;

use A17\Twill\Commands\Install;
use Illuminate\Support\ServiceProvider;

class TwillInstallServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Install::class,
            ]);
        }
    }
}
