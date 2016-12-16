<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use A17\CmsToolkit\Models\Enums\UserRole;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class UserController extends ModuleController
{
    protected $namespace = 'A17\CmsToolkit';

    protected $moduleName = 'users';
    protected $indexWith = ['medias'];
    protected $defaultOrders = ['name' => 'asc'];
    protected $defaultFilters = [
        'fSearch' => 'name',
    ];
    protected $filters = [
        'fRole' => 'role',
    ];

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->removeMiddleware('can:edit');
        $this->middleware('can:edit-user,user', ['only' => ['create', 'store', 'edit', 'update', 'media', 'file']]);
    }

    protected function indexData($request)
    {
        return [
            'fRoleList' => [null => 'All roles'] + UserRole::toArray(),
        ];
    }

    protected function formData($request)
    {
        return [
            'roleList' => UserRole::toArray(),
        ];
    }
}
