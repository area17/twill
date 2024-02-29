<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\SingletonModuleController as BaseModuleController;

class AboutController extends BaseModuleController
{
    protected $moduleName = 'abouts';

    protected $indexOptions = [
        'permalink' => false,
    ];
}
