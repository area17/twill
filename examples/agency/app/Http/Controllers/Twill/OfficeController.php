<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;

class OfficeController extends BaseModuleController
{
    protected $moduleName = 'offices';

    protected $indexOptions = [
        'permalink' => false,
    ];
}
