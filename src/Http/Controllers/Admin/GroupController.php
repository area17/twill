<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Facades\TwillPermissions;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Services\Listings\TableColumns;
use Illuminate\Http\JsonResponse;
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


    protected function getIndexOption($option, $item = null): mixed
    {
        if (in_array($option, ['bulkEdit', 'create'])) {
            return auth('twill_users')->user()->can('edit-user-groups');
        }

        return parent::getIndexOption($option);
    }

    protected function formData($request): array
    {
        return [
            'permissionModules' => Permission::permissionableParentModuleItems(),
        ];
    }

    protected function indexItemData($item): array
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
            return ! $item->isEveryoneGroup();
        })->values();
    }

    public function edit(int|TwillModelContract $id): mixed
    {
        $this->authorizableOptions['edit'] = 'edit-group';

        return parent::edit($id);
    }

    public function update(int|TwillModelContract $id, ?int $submoduleId = null): JsonResponse
    {
        $this->authorizableOptions['edit'] = 'edit-group';

        return parent::update($id, $submoduleId);
    }
}
