<?php

namespace A17\Twill\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function removeMiddleware($middleware)
    {
        if (($key = array_search($middleware, array_pluck($this->middleware, 'middleware'))) !== false) {
            unset($this->middleware[$key]);
        }

    }
}
