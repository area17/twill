<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Exceptions\Handler as TwillHandler;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Application $app, Config $config)
    {
        if ($config->get('twill.bind_exception_handler', true)) {
            $app->singleton(ExceptionHandler::class, TwillHandler::class);
        }
    }

    public function removeMiddleware($middleware)
    {
        if (($key = array_search($middleware, Arr::pluck($this->middleware, 'middleware'))) !== false) {
            unset($this->middleware[$key]);
        }
    }
}
