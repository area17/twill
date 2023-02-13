<?php

namespace A17\Twill\Facades;

use A17\Twill\Routing\PendingRegistration;
use Illuminate\Support\Facades\Facade;

/**
 * @method static PendingRegistration module(string $slug, array $options = [])
 * @method static PendingRegistration singleton(string $slug, array $options = [])
 * @method static vod moduleShowWithPreview(string $moduleName, string $routePrefix = null, string $controllerName = null)
 *
 * @see \A17\Twill\Routing\TwillRoutes
 */
class TwillRoutes extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \A17\Twill\Routing\TwillRoutes::class;
    }
}
