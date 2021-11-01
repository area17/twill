<?php

namespace A17\Twill\Tests\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigInjectingProvider extends ServiceProvider
{
    public static $configToInject = [];

    public function register(): void
    {
        foreach (self::$configToInject as $key => $value) {
            config()->set($key, $value);
        }
    }
}
