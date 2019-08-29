<?php

namespace A17\Twill\Http\Controllers\Front;

use A17\Twill\Services\MediaLibrary\Glide;
use Illuminate\Foundation\Application;

class GlideController
{
    public function __invoke($path, Application $app)
    {
        return $app->get(Glide::class)->render($path);
    }
}
