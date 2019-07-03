<?php

namespace A17\Twill\Http\Controllers\Front;

use A17\Twill\Exceptions\Handler as TwillHandler;
use A17\Twill\Http\Controllers\Front\Helpers\Seo;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\Factory as ViewFactory;

class Controller extends BaseController
{
    /**
     * @var Seo
     */
    public $seo;

    public function __construct(Application $app, ViewFactory $view, Config $config)
    {
        if ($config->get('twill.bind_exception_handler', true)) {
            $app->singleton(ExceptionHandler::class, TwillHandler::class);
        }

        $this->seo = new Seo;

        $this->seo->title = $config->get('twill.seo.site_title');
        $this->seo->description = $config->get('twill.seo.site_desc');
        $this->seo->width = 900;
        $this->seo->height = 470;

        $view->share('seo', $this->seo);
    }
}
