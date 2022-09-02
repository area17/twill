<?php

namespace A17\Twill\Facades;

use Illuminate\Support\Facades\Facade;

class TwillNavigation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \A17\Twill\TwillNavigation::class;
    }
}
