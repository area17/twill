<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;

class CommentController extends BaseModuleController
{
    protected $moduleName = 'comments';

    protected $indexOptions = [
        'permalink' => false,
    ];
}
