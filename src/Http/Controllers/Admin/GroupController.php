<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Facades\TwillPermissions;
use Illuminate\Http\Request;
use A17\Twill\Models\Permission;
use Illuminate\Contracts\Foundation\Application;

class GroupController extends ModuleController
{
    protected $namespace = 'A17\Twill';

    protected $moduleName = 'groups';

    protected $defaultOrders = ['name' => 'asc'];

    protected $titleColumnKey = 'name';

    protected $indexOptions = [
        'permalink' => false,
    ];

    protected $labels = [
        'published' => 'twill::lang.permissions.groups.published',
        'draft' => 'twill::lang.permissions.groups.draft',
        'listing' => [
            'filter' => [
                'published' => 'twill::lang.permissions.groups.published',
                'draft' => 'twill::lang.permissions.groups.draft',
            ],
        ],
    ];

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->middleware('can:edit-user-groups');

        TwillPermissions::showUserSecondaryNavigation();
    }

    protected $indexColumns = [
        'name' => [
            'title' => 'Name',
            'field' => 'name',
            'sort' => true,
        ],
        'created_at' => [
            'title' => 'Date created',
            'field' => 'created_at',
            'sort' => true,
        ],
        'users' => [
            'title' => 'Users',
            'field' => 'users_count',
            'html' => true,
        ],
    ];

    protected function getIndexOption($option, $item = null)
    {
        if (in_array($option, ['publish', 'bulkEdit', 'create'])) {
            return auth('twill_users')->user()->can('edit-user-groups');
        }

        return parent::getIndexOption($option);
    }

    protected function formData($request)
    {
        return [
            'permissionModules' => Permission::permissionableParentModuleItems(),
        ];
    }

    protected function indexItemData($item)
    {
        $canEdit = auth('twill_users')->user()->can('edit-user-groups') && ($item->canEdit ?? true);

        return ['edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null];
    }

    protected function getIndexItems($scopes = [], $forcePagination = false)
    {
        // Everyone group should always be on top
        return parent::getIndexItems($scopes, $forcePagination)->sortByDesc('is_everyone_group')->values();
    }

    protected function getBrowserItems($scopes = [])
    {
        // Exclude everyone group from browsers
        return parent::getBrowserItems($scopes)->filter(function ($item) {
            return !$item->isEveryoneGroup();
        })->values();
    }

    public function edit($id, $submoduleId = null)
    {
        $this->authorizableOptions['edit'] = 'edit-group';

        return parent::edit($id, $submoduleId);
    }

    public function update($id, $submoduleId = null)
    {
        $this->authorizableOptions['edit'] = 'edit-group';

        return parent::update($id, $submoduleId);
    }
}
