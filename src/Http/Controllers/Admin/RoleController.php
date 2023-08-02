<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Facades\TwillPermissions;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\Permission;
use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Services\Listings\TableColumns;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends ModuleController
{
    protected $namespace = 'A17\Twill';

    protected $moduleName = 'roles';

    protected $indexWith = ['medias'];

    protected $defaultOrders = ['name' => 'asc'];

    protected $titleColumnKey = 'name';

    protected $indexOptions = [
        'permalink' => false,
    ];

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

        $this->disablePublish();

        TwillPermissions::showUserSecondaryNavigation();
    }

    protected function getIndexTableColumns(): TableColumns
    {
        return TableColumns::make([
            Text::make()->field('name')->sortable()->title('Name')->linkToEdit(),
            Text::make()->field('created_at')->sortable()->title('Date created'),
            Text::make()->field('users_count')->sortable()->title('Users'),
        ]);
    }

    protected function getIndexItems($scopes = [], $forcePagination = false)
    {
        $scopes += ['accessible' => true];

        return parent::getIndexItems($scopes, $forcePagination);
    }

    protected function getIndexOption($option, $item = null)
    {
        if (in_array($option, ['bulkEdit', 'create'])) {
            return auth('twill_users')->user()->can('edit-user-roles');
        }

        return parent::getIndexOption($option);
    }

    protected function formData($request): array
    {
        return [
            'permission_modules' => Permission::permissionableParentModuleItems(),
        ];
    }

    protected function indexItemData($item): array
    {
        $canEdit = auth('twill_users')->user()->can('edit-user-roles') && ($item->canEdit ?? true);

        return ['edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null];
    }

    public function index(?int $parentModuleId = null): mixed
    {
        // Superadmins can reorder groups to determine the access-level of each one.
        // A given group can't edit other groups with a higher access-level.
        $this->indexOptions['reorder'] = auth('twill_users')->user()->isSuperAdmin();

        return parent::index($parentModuleId);
    }

    public function edit(int|TwillModelContract $id): mixed
    {
        $this->authorizableOptions['edit'] = 'edit-role';

        return parent::edit($id);
    }

    public function update(int|TwillModelContract $id, ?int $submoduleId = null): JsonResponse
    {
        $this->authorizableOptions['edit'] = 'edit-role';

        return parent::update($id, $submoduleId);
    }
}
