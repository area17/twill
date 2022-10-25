<?php

namespace App\Http\Controllers\Admin;

use A17\Twill\Http\Controllers\Admin\ModuleController;

class ProfileController extends ModuleController
{
    protected $moduleName = 'profiles';

    protected $titleColumnKey = 'name';

    protected $indexOptions = [
        'create' => false,
        'delete' => false,
    ];
}
