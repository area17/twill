<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Group;
use Illuminate\Http\Request;
use A17\Twill\Models\Permission;
use Illuminate\Contracts\Foundation\Application;

class GroupController extends ModuleController
{
    /**
     * @var string
     */
    protected $namespace = 'A17\Twill';

    /**
     * @var string
     */
    protected $moduleName = 'groups';

    /**
     * @var array<string, string>
     */
    protected $defaultOrders = ['name' => 'asc'];

    /**
     * @var array<string, string>
     */
    protected $defaultFilters = [
        'search' => 'search',
    ];

    /**
     * @var string
     */
    protected $titleColumnKey = 'name';

    /**
     * @var array<string, false>
     */
    protected $indexOptions = [
        'permalink' => false,
    ];

    /**
     * @var array<string, mixed[]>
     */
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

        $this->primaryNavigation = [
            'users' => [
                'title' => twillTrans('twill::lang.user-management.users'),
                'module' => true,
                'can' => 'edit-users',
            ],
            'roles' => [
                'title' => twillTrans('twill::lang.permissions.roles.title'),
                'module' => true,
                'can' => 'edit-user-roles',
            ],
            'groups' => [
                'title' => twillTrans('twill::lang.permissions.groups.title'),
                'module' => true,
                'active' => true,
                'can' => 'edit-user-groups',
            ],
        ];
    }

    /**
     * @var array<string, mixed[]>
     */
    protected $indexColumns = [
        'name' => [
            'title' => 'Name',
            'field' => 'name',
            'sort' => true,
        ],
        'created_at' => [
            'title' => 'Date created',
            'field' => 'created_at',
            'sort' => true
        ],
        'users' => [
            'title' => 'Users',
            'field' => 'users_count',
            'html' => true
        ]
    ];

    /**
     * @return array<string, mixed[]>
     */
    protected function indexData($request): array
    {
        return [
            'primary_navigation' => $this->primaryNavigation,
        ];
    }

    protected function getIndexOption($option, $item = null)
    {
        if (in_array($option, ['publish', 'bulkEdit', 'create'])) {
            return auth('twill_users')->user()->can('edit-user-groups');
        }

        return parent::getIndexOption($option);
    }

    /**
     * @return array<string, \A17\Twill\Models\Collection>|array<string, mixed[]>
     */
    protected function formData($request): array
    {
        return [
            'primary_navigation' => $this->primaryNavigation,
            'permissionModules' => Permission::permissionableParentModuleItems(),
        ];
    }

    /**
     * @return null[]|array<string, string>
     */
    protected function indexItemData($item): array
    {
        $canEdit = auth('twill_users')->user()->can('edit-user-groups') && ($item->canEdit ?? true);

        return ['edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null];
    }

    protected function getIndexItems($scopes = [], $forcePagination = false): \Illuminate\Support\Collection
    {
        // Everyone group should always be on top
        return parent::getIndexItems($scopes, $forcePagination)->sortByDesc('is_everyone_group')->values();
    }

    protected function getBrowserItems($scopes = []): \Illuminate\Support\Collection
    {
        // Exclude everyone group from browsers
        return parent::getBrowserItems($scopes)->filter(function ($item): bool {
            return !$item->isEveryoneGroup();
        })->values();
    }

    public function edit($id, $submoduleId = null): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
    {
        $this->authorizableOptions['edit'] = 'edit-group';

        return parent::edit($id, $submoduleId);
    }

    public function update($id, $submoduleId = null): \Illuminate\Http\JsonResponse
    {
        $this->authorizableOptions['edit'] = 'edit-group';

        return parent::update($id, $submoduleId);
    }
}
