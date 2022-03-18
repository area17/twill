<?php

namespace App\Twill\Capsules\Posts\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use A17\Twill\Http\Controllers\Admin\ModuleController;

class PostController extends ModuleController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $moduleName = 'posts';
}
