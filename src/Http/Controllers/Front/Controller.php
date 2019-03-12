<?php

namespace Sb4yd3e\Twill\Http\Controllers\Front;

use Sb4yd3e\Twill\Exceptions\Handler as TwillHandler;
use Sb4yd3e\Twill\Http\Controllers\Front\Helpers\Seo;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Routing\Controller as BaseController;
use View;

class Controller extends BaseController
{
    public $seo;

    public function __construct()
    {
        if (config('twill.bind_exception_handler', true)) {
            app()->singleton(ExceptionHandler::class, TwillHandler::class);
        }

        $this->seo = new Seo;

        $this->seo->title = config('twill.seo.site_title');
        $this->seo->description = config('twill.seo.site_desc');
        $this->seo->width = 900;
        $this->seo->height = 470;

        View::share('seo', $this->seo);
    }

}
