<?php

namespace A17\Twill\Facades;

use Illuminate\Support\Facades\Facade;

class Route extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'router';
    }

    public static function singleton($moduleName)
    {
        return app(static::getFacadeAccessor())->twillSingleton($moduleName);
    }
}
