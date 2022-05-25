<?php

namespace A17\Twill\Facades;

use Illuminate\Support\Facades\Facade;

class TwillBlocks extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \A17\Twill\TwillBlocks::class;
    }
}
