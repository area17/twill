<?php

namespace A17\Twill;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

class CapsulesServiceProvider extends RouteServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/capsules.php',
            'twill.capsules'
        );
    }
}
