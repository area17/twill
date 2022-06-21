<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;

class LinkController extends BaseModuleController
{
    protected $moduleName = 'links';

    protected $indexOptions = [
        'permalink' => false,
    ];
}
