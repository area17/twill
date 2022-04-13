<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;

class PersonController extends BaseModuleController
{
    protected $moduleName = 'people';

    protected $indexOptions = [
    ];

    protected $titleColumnKey = 'full_name';
}
