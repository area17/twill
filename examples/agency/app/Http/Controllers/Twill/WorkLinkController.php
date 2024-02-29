<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;

class WorkLinkController extends BaseModuleController
{
    protected $moduleName = 'workLinks';

    protected $indexOptions = [
        'permalink' => false,
    ];
}
