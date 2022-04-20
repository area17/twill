<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\NestedModuleController as ModuleController;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;

class NodeController extends ModuleController
{
    public static $forceShowOnlyParentItemsInBrowsers = true;

    protected $moduleName = 'nodes';

    protected $indexOptions = [
        'permalink' => false,
        'reorder' => true,
    ];

    public function __construct(Application $app, Request $request)
    {
        $this->showOnlyParentItemsInBrowsers = self::$forceShowOnlyParentItemsInBrowsers;

        parent::__construct($app, $request);
    }
}
