<?php

namespace A17\Twill\Exceptions;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

/** @deprecated It is not needed anymore and will be removed in v4 */
class Handler extends ExceptionHandler
{
    public function __construct(Container $container)
    {
        parent::__construct($container);

        trigger_deprecation('area17/twill', '3.4', 'The Twill Exception handler is deprecated and will be removed in v4, go back to extending the laravel ExceptionHandler');
    }
}
