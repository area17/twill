<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Facades\TwillPermissions;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\Group;
use A17\Twill\Models\Permission;
use A17\Twill\Models\Role;
use A17\Twill\Models\User;
use A17\Twill\Services\Listings\Columns\Image;
use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Services\Listings\Filters\QuickFilter;
use A17\Twill\Services\Listings\Filters\QuickFilters;
use A17\Twill\Services\Listings\TableColumns;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserController extends ModuleController
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var AuthFactory
     */
    protected $authFactory;

    /**
     * @var string
     */
    protected $namespace = 'A17\Twill';

    /**
     * @var string
     */
    protected $moduleName = 'users';

    /**
     * @var string[]
     */
    protected $indexWith = ['medias'];

    /**
     * @var array
     */
    protected $defaultOrders = ['name' => 'asc'];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var string
     */
    protected $titleColumnKey = 'name';

    /**
     * @var array
     */
    protected $indexOptions = [
        'permalink' => false,
    ];

    /**
     * @var array
     */
    protected $fieldsPermissions = [
        'role' => 'manage-users',
    ];

    protected $labels = [
        'published' => 'twill::lang.user-management.enabled',
        'draft' => 'twill::lang.user-management.disabled',
        'listing' => [
            'filter' => [
                'published' => 'twill::lang.user-management.enabled',
                'draft' => 'twill::lang.user-management.disabled',
            ],
        ],
    ];

    public function __construct(Application $app, Request $request, AuthFactory $authFactory, Config $config)
    {
        parent::__construct($app, $request);
        $this->middleware('can:edit-users', ['except' => ['edit', 'update']]);
        $this->authFactory = $authFactory;
        $this->config = $config;

        TwillPermissions::showUserSecondaryNavigation();

        $this->filters['role'] = User::getRoleColumnName();
    }

    public function getIndexTableColumns(): TableColumns
    {
        $tableColumns = TableColumns::make();
        if ($this->config->get('twill.enabled.users-image')) {
            $tableColumns->add(
                Image::make()
                    ->field('image')
                    ->title('Image')
                    ->rounded()
            );
        }

        $tableColumns->add(
            Text::make()
                ->field($this->titleColumnKey)
                ->linkToEdit()
                ->sortable(),
        );
        $tableColumns->add(
            Text::make()
                ->field('last_login_at')
                ->title('Last Login')
                ->customRender(function (TwillModelContract $user) {
                    return $user->last_login_at ? $user->last_login_at->ago() : '-';
                })
                ->sortable()
        );
        $tableColumns->add(
            Text::make()
                ->field('email')
                ->title('Email')
                ->sortable()
        );
        $tableColumns->add(
            Text::make()
                ->field(User::getRoleColumnName())
                ->title('Role')
                ->customRender(function (TwillModelContract $user) {
                    if (TwillPermissions::enabled()) {
                        return Str::title($user->role->name);
                    }
                    return Str::title($user->role);
                })
                ->sortable()
        );

        return $tableColumns;
    }

    public function setUpController(): void
    {
        $this->setSearchColumns(['name', 'email']);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function indexData($request)
    {
        return [
            'defaultFilterSlug' => 'activated',
            'create' => $this->getIndexOption('create') && $this->user->can('edit-users'),
            'roleList' => $this->getRoleList(),
        ];
    }

    protected function getIndexItems(array $scopes = [], bool $forcePagination = false)
    {
        if (TwillPermissions::enabled()) {
            $scopes += ['accessible' => true];
        }

        return parent::getIndexItems($scopes, $forcePagination);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     */
    protected function formData($request)
    {
        $currentUser = $this->authFactory->guard('twill_users')->user();
        $with2faSettings = $this->config->get('twill.enabled.users-2fa') && $currentUser->id == $request->route('user');

        if ($with2faSettings) {
            $currentUser->generate2faSecretKey();

            $qrCode = $currentUser->get2faQrCode();
        }

        // Get user thumbnail (fixme because this always return the fallback blank image)
        if ($this->config->get('twill.enabled.users-image')) {
            $user = $this->repository->getById($request->route('user'));
            $role = head(array_keys($user->mediasParams));
            $crop = head(array_keys(head($user->mediasParams)));
            $params = ['w' => 100, 'h' => 100];
            $titleThumbnail = $user->cmsImage($role, $crop, $params);
        }

        return [
            'roleList' => $this->getRoleList(),
            'titleThumbnail' => $titleThumbnail ?? null,
            'permissionModules' => $this->getPermissionModules(),
            'groupPermissionMapping' => $this->getGroupPermissionMapping(),
            'with2faSettings' => $with2faSettings,
            'qrCode' => $qrCode ?? null,
            'groupOptions' => $this->getGroups(),
        ];
    }

    /**
     * @return array
     */
    protected function getRequestFilters(): array
    {
        if ($this->request->has('search')) {
            return ['search' => $this->request->get('search')];
        }

        return json_decode($this->request->get('filter'), true) ?? ['status' => 'activated'];
    }

    protected function getDefaultQuickFilters(): QuickFilters
    {
        if (config('twill.enabled.permissions-management')) {
            $roleScope = ['is_superadmin', false];
        } else {
            $roleScope = ['role', '<>', 'SUPERADMIN'];
        }

        $filters = QuickFilters::make([
            QuickFilter::make()
                ->label(twillTrans('twill::lang.user-management.active'))
                ->queryString('activated')
                ->scope('activated')
                ->amount(fn() => $this->repository->getCountByStatusSlug('activated', [$roleScope])),
            QuickFilter::make()
                ->label(twillTrans('twill::lang.user-management.pending'))
                ->queryString('pending')
                ->scope('pending')
                ->amount(fn() => $this->repository->getCountByStatusSlug('pending', [$roleScope])),
            QuickFilter::make()
                ->label(twillTrans('twill::lang.user-management.disabled'))
                ->queryString('draft')
                ->scope('draft')
                ->amount(fn() => $this->repository->getCountByStatusSlug('draft', [$roleScope])),
        ]);

        if ($this->getIndexOption('restore')) {
            $filters->add(
                QuickFilter::make()
                    ->label(twillTrans('twill::lang.user-management.trash'))
                    ->queryString('trash')
                    ->scope('onlyTrashed')
                    ->amount(fn() => $this->repository->getCountByStatusSlug('trash', [$roleScope])),
            );
        }

        return $filters;
    }

    /**
     * @param string $option
     * @return bool
     */
    protected function getIndexOption($option, $item = null)
    {
        if (in_array($option, ['publish', 'bulkEdit', 'create'])) {
            return $this->authFactory->guard('twill_users')->user()->can('edit-users');
        }

        return parent::getIndexOption($option);
    }

    /**
     * @param \A17\Twill\Models\ModelInterface $item
     * @return array
     */
    protected function indexItemData($item)
    {
        $canEdit = $this->user->can('edit-users');

        return ['edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null];
    }

    public function edit(int|TwillModelContract $id): mixed
    {
        $this->authorizableOptions['edit'] = 'edit-user';

        return parent::edit($id);
    }

    public function update(int|TwillModelContract $id, ?int $submoduleId = null): JsonResponse
    {
        $this->authorizableOptions['edit'] = 'edit-user';

        return parent::update($id, $submoduleId);
    }

    public function resendRegistrationEmail($userId)
    {
        $user = twillModel('user')::findOrFail($userId);
        $user->sendWelcomeNotification(
            Password::broker('twill_users')->getRepository()->create($user)
        );

        return redirect()->route('twill.users.edit', ['user' => $user])->with(
            'status',
            'Registration email has been sent to the user!'
        );
    }

    private function getGroupPermissionMapping()
    {
        if (config('twill.enabled.permissions-management')) {
            return Group::with('permissions')->get()
                ->mapWithKeys(function ($group) {
                    return [$group->id => $group->permissions];
                })->toArray();
        }

        return [];
    }

    private function getGroups()
    {
        if (config('twill.enabled.permissions-management')) {
            // Forget first one because it's the "Everyone" group and we don't want to show it inside admin.
            return Group::with('permissions')->pluck('name', 'id')->forget(1);
        }

        return [];
    }

    private function getRoleList()
    {
        if (config('twill.enabled.permissions-management')) {
            return Role::accessible()->published()->get()->map(function ($role) {
                return ['value' => $role->id, 'label' => $role->name];
            })->toArray();
        }

        return collect(TwillPermissions::roles()::toArray())->map(function ($item, $key) {
            return ['value' => $key, 'label' => $item];
        })->values()->toArray();
    }

    private function getPermissionModules()
    {
        if (config('twill.enabled.permissions-management')) {
            return Permission::permissionableParentModuleItems();
        }

        return [];
    }

    public function getSubmitOptions(Model $item): ?array
    {
        // Use options from form template
        return null;
    }
}
