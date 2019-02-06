<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Enums\UserRole;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class GroupController extends ModuleController
{
    protected $namespace = 'A17\Twill';

    protected $moduleName = 'groups';

    protected $indexWith = ['medias'];

    protected $defaultOrders = ['title' => 'asc'];

    protected $defaultFilters = [
        'search' => 'search',
    ];

    protected $indexOptions = [
        'permalink' => false,
    ];

}
