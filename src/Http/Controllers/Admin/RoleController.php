<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Permission;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class RoleController extends ModuleController
{
    /**
     * @var string
     */
    protected $namespace = 'A17\Twill';

    /**
     * @var string
     */
    protected $moduleName = 'roles';

    /**
     * @var string[]
     */
    protected $indexWith = ['medias'];

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
        'published' => 'twill::lang.permissions.roles.published',
        'draft' => 'twill::lang.permissions.roles.draft',
        'listing' => [
            'filter' => [
                'published' => 'twill::lang.permissions.roles.published',
                'draft' => 'twill::lang.permissions.roles.draft',
            ],
        ],
    ];

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->middleware('can:edit-user-roles');

        $this->primaryNavigation = [
            'users' => [
                'title' => twillTrans('twill::lang.user-management.users'),
                'module' => true,
                'can' => 'edit-users',
            ],
            'roles' => [
                'title' => twillTrans('twill::lang.permissions.roles.title'),
                'module' => true,
                'active' => true,
                'can' => 'edit-user-roles',
            ],
            'groups' => [
                'title' => twillTrans('twill::lang.permissions.groups.title'),
                'module' => true,
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

    protected function getIndexItems($scopes = [], $forcePagination = false): \Illuminate\Database\Eloquent\Collection
    {
        $scopes += ['accessible' => true];

        return parent::getIndexItems($scopes, $forcePagination);
    }

    protected function getIndexOption($option, $item = null)
    {
        if (in_array($option, ['publish', 'bulkEdit', 'create'])) {
            return auth('twill_users')->user()->can('edit-user-roles');
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
            'permission_modules' => Permission::permissionableParentModuleItems(),
        ];
    }

    /**
     * @return null[]|array<string, string>
     */
    protected function indexItemData($item): array
    {
        $canEdit = auth('twill_users')->user()->can('edit-user-roles') && ($item->canEdit ?? true);

        return ['edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null];
    }

    public function index($parentModuleId = null): array|\Illuminate\View\View
    {
        // Superadmins can reorder groups to determine the access-level of each one.
        // A given group can't edit other groups with a higher access-level.
        $this->indexOptions['reorder'] = auth('twill_users')->user()->isSuperAdmin();

        return parent::index($parentModuleId);
    }

    public function edit($id, $submoduleId = null): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
    {
        $this->authorizableOptions['edit'] = 'edit-role';

        return parent::edit($id, $submoduleId);
    }

    public function update($id, $submoduleId = null): \Illuminate\Http\JsonResponse
    {
        $this->authorizableOptions['edit'] = 'edit-role';

        return parent::update($id, $submoduleId);
    }
}
