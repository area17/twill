<?php

namespace A17\CmsToolkit;

use A17\CmsToolkit\Commands\Install;
use Illuminate\Support\ServiceProvider;

class CmsToolkitInstallServiceProvider extends ServiceProvider
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
