<?php

namespace A17\CmsToolkit\Http\Controllers\Front;

use A17\CmsToolkit\Http\Controllers\Front\Helpers\Seo;
use Illuminate\Routing\Controller as BaseController;
use View;

class Controller extends BaseController
{
    public $seo;

    public function __construct()
    {
        $this->seo = new Seo;

        $this->seo->title = config('cms-toolkit.seo.site_title');
        $this->seo->description = config('cms-toolkit.seo.site_desc');
        $this->seo->width = 900;
        $this->seo->height = 470;

        View::share('seo', $this->seo);
    }

}
