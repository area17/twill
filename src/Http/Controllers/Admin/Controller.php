<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Exceptions\Handler as TwillHandler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        if (config('twill.bind_exception_handler', true)) {
            app()->singleton(ExceptionHandler::class, TwillHandler::class);
        }

    }

    public function removeMiddleware($middleware)
    {
        if (($key = array_search($middleware, array_pluck($this->middleware, 'middleware'))) !== false) {
            unset($this->middleware[$key]);
        }

    }
}
