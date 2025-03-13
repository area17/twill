<?php

namespace A17\Twill\Http\Controllers\Front;

use A17\Twill\Exceptions\Handler as TwillHandler;
use A17\Twill\Http\Controllers\Front\Helpers\Seo;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    /**
     * @var Seo
     */
    public $seo;

    public function __construct()
    {
        $this->seo = new Seo();

        $this->seo->title = Config::get('twill.seo.site_title');
        $this->seo->description = Config::get('twill.seo.site_desc');
        $this->seo->width = 900;
        $this->seo->height = 470;

        View::share('seo', $this->seo);
    }
}
