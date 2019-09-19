<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Permission;
use A17\Twill\Models\Role;
use A17\Twill\Models\User;
use A17\Twill\Models\Group;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Password;
use PragmaRX\Google2FAQRCode\Google2FA;

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
    protected $defaultFilters = [
        'search' => 'search',
    ];

    /**
     * @var array
     */
    protected $filters = [
        'role' => 'role',
    ];

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
            'sort' => true
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
            'sortKey' => 'role_id',
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

    public function __construct(Application $app, Request $request, AuthFactory $authFactory, Config $config)
    {
        parent::__construct($app, $request);
        $this->middleware('can:edit-users', ['except' => ['edit', 'update']]);
        $this->authFactory = $authFactory;
        $this->config = $config;

        if ($this->config->get('twill.enabled.users-image')) {
            $this->indexColumns = [
                'image' => [
                    'title' => 'Image',
                    'thumb' => true,
                    'variation' => 'rounded',
                    'variant' => [
                        'role' => 'profile',
                        'crop' => 'default',
                        'shape' => 'rounded'
                    ]
                ],
            ] + $this->indexColumns;
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function indexData($request)
    {
        return [
            'create' => $this->getIndexOption('create') && $this->user->can('edit-users'),
            'roleList' => Role::published()->get()->map(function ($role) {
                return ['value' => $role->id, 'label' => $role->name];
            })->toArray(),
            'primary_navigation' => [
                'users' => [
                    'title' => 'Users',
                    'module' => true,
                    'active' => true,
                    'can' => 'edit-users',
                ],
                'roles' => [
                    'title' => 'Roles',
                    'module' => true,
                    'can' => 'edit-user-role',
                ],
                'groups' => [
                    'title' => 'Groups',
                    'module' => true,
                    'can' => 'edit-user-groups',
                ],
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
        ];
    }

    /**
     * @param Request $request
     * @return array
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     */
    protected function formData($request)
    {
        $user = $this->authFactory->guard('twill_users')->user();
        $with2faSettings = $this->config->get('twill.enabled.users-2fa') && $user->id == $this->request->get('user');

        if ($with2faSettings) {
            $google2fa = new Google2FA();

            if (is_null($user->google_2fa_secret)) {
                $secret = $google2fa->generateSecretKey();
                $user->google_2fa_secret = \Crypt::encrypt($secret);
                $user->save();
            }

            $qrCode = $google2fa->getQRCodeInline(
                $this->config->get('app.name'),
                $user->email,
                \Crypt::decrypt($user->google_2fa_secret),
                200
            );
        }

        return [
            'roleList' => Role::published()->get()->map(function ($role) {
                return ['value' => $role->id, 'label' => $role->name];
            })->toArray(),
            'primary_navigation' => [
                'users' => [
                    'title' => 'Users',
                    'module' => true,
                    'active' => true,
                    'can' => 'edit-users',
                ],
                'roles' => [
                    'title' => 'Roles',
                    'module' => true,
                    'can' => 'edit-user-role',
                ],
                'groups' => [
                    'title' => 'Groups',
                    'module' => true,
                    'can' => 'edit-user-groups',
                ],
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
            'permissionModules' => Permission::permissionableParentModuleItems(),
            'groupPermissionMapping' => $this->getGroupPermissionMapping(),
            'with2faSettings' => $with2faSettings,
            'qrCode' => $qrCode ?? null,
            'groupOptions' => $this->getGroups()
        ];
    }

    /**
     * @return array
     */
    protected function getRequestFilters()
    {
        return json_decode($this->request->get('filter'), true) ?? ['status' => 'published'];
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $items
     * @param array $scopes
     * @return array
     */
    public function getIndexTableMainFilters($items, $scopes = [])
    {
        $statusFilters = [];

        array_push($statusFilters, [
            'name' => 'Active',
            'slug' => 'published',
            'number' => $this->repository->getCountByStatusSlug('published', [['is_superadmin', false]]),
        ], [
            'name' => 'Disabled',
            'slug' => 'draft',
            'number' => $this->repository->getCountByStatusSlug('draft'),
        ]);

        if ($this->getIndexOption('restore')) {
            array_push($statusFilters, [
                'name' => 'Trash',
                'slug' => 'trash',
                'number' => $this->repository->getCountByStatusSlug('trash'),
            ]);
        }

        return $statusFilters;
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
     * @param \A17\Twill\Models\Model $item
     * @return array
     */
    protected function indexItemData($item)
    {
        $canEdit = $this->user->can('edit-users');

        return ['edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null];
    }

    public function edit($id, $submoduleId = null)
    {
        if ($id !== (string) $this->user->id) {
            $this->authorize('edit-users');
        }

        return parent::edit($id, $submoduleId);
    }

    public function update($id, $submoduleId = null)
    {
        if ($id !== (string) $this->user->id) {
            $this->authorize('edit-users');
        }

        return parent::update($id, $submoduleId);
    }

    public function resendRegistrationEmail(User $user)
    {
        $user->sendWelcomeNotification(
            Password::broker('twill_users')->getRepository()->create($user)
        );
        return redirect()->route('admin.users.edit', ['user' => $user])->with('status', 'Registration email has been sent to the user!');
    }

    private function getGroupPermissionMapping()
    {
        return Group::with('permissions')->get()
        ->mapWithKeys(function($group) {
            return [ $group->id => $group->permissions ];
        })->toArray();
    }

    private function getGroups()
    {
        // Forget first one because it's the "Everyone" group and we don't want to show it inside admin.
        return Group::with('permissions')->get()->pluck('name','id')->forget(1);
    }
}
