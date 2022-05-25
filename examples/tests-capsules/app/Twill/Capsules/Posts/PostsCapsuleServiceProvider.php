<?php

namespace App\Twill\Capsules\Posts;

use Illuminate\Support\ServiceProvider;

class PostsCapsuleServiceProvider extends ServiceProvider
{
    public static bool $isBooted = false;

    public function boot(): void
    {
        self::$isBooted = true;
    }

    public function register(): void
    {
    }
}
