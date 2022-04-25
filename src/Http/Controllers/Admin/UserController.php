<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Enums\UserRole;
use A17\Twill\Models\Group;
use A17\Twill\Models\Permission;
use A17\Twill\Models\Role;
use A17\Twill\Models\User;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class UserController extends ModuleController
{
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
    protected $defaultFilters = [
        'search' => 'search',
    ];

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
    protected $indexColumns = [
        'name' => [
            'title' => 'Name',
            'field' => 'name',
            'sort' => true,
        ],
        'last_login' => [
            'title' => 'Last Login',
            'field' => 'last_login_column_value',
            'sort' => true,
        ],
        'email' => [
            'title' => 'Email',
            'field' => 'email',
            'sort' => true,
        ],
        'role_value' => [
            'title' => 'Role',
            'field' => 'role_value',
            'sort' => true,
        ],
    ];

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

    /**
     * @var array<string, mixed[]>
     */
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

    public function __construct(Application $app, Request $request, protected AuthFactory $authFactory, protected Config $config)
    {
        parent::__construct($app, $request);
        $this->middleware('can:edit-users', ['except' => ['edit', 'update']]);

        if ($this->config->get('twill.enabled.users-image')) {
            $this->indexColumns = [
                'image' => [
                    'title' => 'Image',
                    'thumb' => true,
                    'variation' => 'rounded',
                    'variant' => [
                        'role' => 'profile',
                        'crop' => 'default',
                        'shape' => 'rounded',
                    ],
                ],
            ] + $this->indexColumns;
        }

        $this->primaryNavigation = [
            'users' => [
                'title' => twillTrans('twill::lang.user-management.users'),
                'module' => true,
                'active' => true,
                'can' => 'edit-users',
            ],
        ] + (config('twill.enabled.permissions-management') ? [
            'roles' => [
                'title' => twillTrans('twill::lang.permissions.roles.title'),
                'module' => true,
                'can' => 'edit-user-roles',
            ],
            'groups' => [
                'title' => twillTrans('twill::lang.permissions.groups.title'),
                'module' => true,
                'can' => 'edit-user-groups',
            ],
        ] : []);

        $this->filters['role'] = User::getRoleColumnName();
        $this->indexColumns['role_value']['sortKey'] = User::getRoleColumnName();
    }

    /**
     * @return array<string, mixed>
     */
    protected function indexData(\Illuminate\Http\Request $request): array
    {
        return [
            'defaultFilterSlug' => 'activated',
            'create' => $this->getIndexOption('create') && $this->user->can('edit-users'),
            'roleList' => $this->getRoleList(),
            'primary_navigation' => $this->primaryNavigation,
        ];
    }

    /**
     * @param mixed[] $scopes
     */
    protected function getIndexItems(array $scopes = [], bool $forcePagination = false): \Illuminate\Database\Eloquent\Collection
    {
        if (config('twill.enabled.permissions-management')) {
            $scopes += ['accessible' => true];
        }

        return parent::getIndexItems($scopes, $forcePagination);
    }

    /**
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @return array<string, mixed>
     */
    protected function formData(\Illuminate\Http\Request $request): array
    {
        $currentUser = $this->authFactory->guard('twill_users')->user();
        $with2faSettings = $this->config->get('twill.enabled.users-2fa') && $currentUser->id == $this->request->get('user');

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
            'primary_navigation' => $this->primaryNavigation,
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
    protected function getRequestFilters()
    {
        if ($this->request->has('search')) {
            return ['search' => $this->request->get('search')];
        }

        return json_decode($this->request->get('filter'), true) ?? ['status' => 'activated'];
    }

    /**
     * @param mixed[] $scopes
     * @return array<int, array{name: mixed, slug: string, number: int}>
     */
    protected function getIndexTableMainFilters(\Illuminate\Database\Eloquent\Collection $items, array $scopes = []): array
    {
        $statusFilters = [];
        $roleScope = config('twill.enabled.permissions-management') ? ['is_superadmin', false] : ['role', '<>', 'SUPERADMIN'];
        $statusFilters[] = [
            'name' => twillTrans('twill::lang.user-management.active'),
            'slug' => 'activated',
            'number' => $this->repository->getCountByStatusSlug('activated', [$roleScope]),
        ];
        $statusFilters[] = [
            'name' => twillTrans('twill::lang.user-management.pending'),
            'slug' => 'pending',
            'number' => $this->repository->getCountByStatusSlug('pending', [$roleScope]),
        ];
        $statusFilters[] = [
            'name' => twillTrans('twill::lang.user-management.disabled'),
            'slug' => 'draft',
            'number' => $this->repository->getCountByStatusSlug('draft', [$roleScope]),
        ];

        if ($this->getIndexOption('restore')) {
            $statusFilters[] = [
                'name' => twillTrans('twill::lang.user-management.trash'),
                'slug' => 'trash',
                'number' => $this->repository->getCountByStatusSlug('trash', [$roleScope]),
            ];
        }

        return $statusFilters;
    }

    /**
     * @return bool
     */
    protected function getIndexOption(string $option, $item = null)
    {
        if (in_array($option, ['publish', 'bulkEdit', 'create'])) {
            return $this->authFactory->guard('twill_users')->user()->can('edit-users');
        }

        return parent::getIndexOption($option);
    }

    /**
     * @return null[]|array<string, string>
     */
    protected function indexItemData(\A17\Twill\Models\Model $item): array
    {
        $canEdit = $this->user->can('edit-users');

        return ['edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null];
    }

    /**
     * @return mixed[]
     */
    protected function filterScope($prepend = []): array
    {
        $scope = [];

        $requestFilters = $this->getRequestFilters();

        if (array_key_exists('status', $requestFilters)) {
            if ($requestFilters['status'] == 'activated') {
                $scope['activated'] = true;
            } elseif ($requestFilters['status'] == 'pending') {
                $scope['pending'] = true;
            }
        }

        return parent::filterScope($prepend + $scope);
    }

    public function edit($id, $submoduleId = null): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
    {
        $this->authorizableOptions['edit'] = 'edit-user';

        return parent::edit($id, $submoduleId);
    }

    public function update($id, $submoduleId = null): \Illuminate\Http\JsonResponse
    {
        $this->authorizableOptions['edit'] = 'edit-user';

        return parent::update($id, $submoduleId);
    }

    public function resendRegistrationEmail($userId): \Illuminate\Http\RedirectResponse
    {
        $user = twillModel('user')::findOrFail($userId);
        $user->sendWelcomeNotification(
            Password::broker('twill_users')->getRepository()->create($user)
        );

        return redirect()->route('twill.users.edit', ['user' => $user])->with('status', 'Registration email has been sent to the user!');
    }

    private function getGroupPermissionMapping()
    {
        if (config('twill.enabled.permissions-management')) {
            return Group::with('permissions')->get()
                ->mapWithKeys(function ($group): array {
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
            return Role::accessible()->published()->get()->map(function ($role): array {
                return ['value' => $role->id, 'label' => $role->name];
            })->toArray();
        }

        return collect(UserRole::toArray())->map(function ($item, $key): array {
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
}
