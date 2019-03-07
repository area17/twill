<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Permission;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class RoleController extends ModuleController
{
    protected $namespace = 'A17\Twill';

    protected $moduleName = 'roles';

    protected $indexWith = ['medias'];

    protected $defaultOrders = ['name' => 'asc'];

    protected $defaultFilters = [
        'search' => 'search',
    ];

    protected $titleColumnKey = 'name';

    protected $indexOptions = [
        'permalink' => false,
    ];

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->removeMiddleware('can:edit');
        $this->removeMiddleware('can:publish');
        $this->middleware('can:edit-user-role');
    }

    protected function indexData($request)
    {
        return [
            'defaultFilterSlug' => 'published',
            'primary_navigation' => [
                'users' => [
                    'title' => 'Users',
                    'module' => true,
                    'can' => 'edit-users',
                ],
                'groups' => [
                    'title' => 'Groups',
                    'module' => true,
                    'can' => 'edit-user-groups',
                ],
                'roles' => [
                    'title' => 'Roles',
                    'module' => true,
                    'active' => true,
                    'can' => 'edit-user-role',
                ],
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
        ];
    }

    protected function getIndexOption($option, $item = null)
    {
        if (in_array($option, ['publish', 'bulkEdit', 'create'])) {
            return auth('twill_users')->user()->can('edit-user-role');
        }

        return parent::getIndexOption($option);
    }

    protected function formData($request)
    {
        $modules_items = Permission::permissionableModules()->mapWithKeys(function ($module) {
            return [$module => getRepositoryByModuleName($module)->get()];
        });

        return [
            'primary_navigation' => [
                'users' => [
                    'title' => 'Users',
                    'module' => true,
                    'can' => 'edit-user-role',
                ],
                'groups' => [
                    'title' => 'Groups',
                    'module' => true,
                    'can' => 'edit-user-role',
                ],
                'roles' => [
                    'title' => 'Roles',
                    'module' => true,
                    'active' => true,
                    'can' => 'edit-user-role',
                ],
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
            'permission_modules' => $modules_items,
        ];
    }

    protected function indexItemData($item)
    {
        $canEdit = auth('twill_users')->user()->can('edit-user-role') && ($item->canEdit ?? true);

        return ['edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null];
    }

    protected function getRequestFilters()
    {
        return json_decode($this->request->get('filter'), true) ?? ['status' => 'published'];
    }

}
