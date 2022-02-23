<?php

namespace A17\Twill\Http\Controllers\Front;

use A17\Twill\Services\MediaLibrary\Glide;
use Illuminate\Foundation\Application;

class GlideController
{
    public function __invoke($path, Application $app)
    {
        /** @var \Symfony\Component\HttpFoundation\StreamedResponse $res */
        $res = $app->make(Glide::class)->render($path);
        $res->headers->add(['Access-Control-Allow-Origin' => '*']);
        return $res;
    }
}
