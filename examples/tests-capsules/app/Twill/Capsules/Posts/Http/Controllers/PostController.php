<?php

namespace App\Twill\Capsules\Posts\Http\Controllers;

use A17\Twill\Http\Controllers\Admin\ModuleController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class PostController extends ModuleController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $moduleName = 'posts';
}
