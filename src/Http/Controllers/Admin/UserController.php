<?php

namespace A17\Twill\Http\Controllers\Admin;

use Auth;
use Password;
use A17\Twill\Models\Permission;
use A17\Twill\Models\Role;
use A17\Twill\Models\User;
use A17\Twill\Models\Group;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use PragmaRX\Google2FAQRCode\Google2FA;

class UserController extends ModuleController
{
    protected $namespace = 'A17\Twill';

    protected $moduleName = 'users';

    protected $indexWith = ['medias'];

    protected $defaultOrders = ['name' => 'asc'];

    protected $defaultFilters = [
        'search' => 'search',
    ];

    protected $filters = [
        'role' => 'role',
    ];

    protected $titleColumnKey = 'name';

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

    protected $indexOptions = [
        'permalink' => false,
    ];

    protected $fieldsPermissions = [
        'role' => 'edit-user-role',
    ];

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->middleware('can:edit-users', ['except' => ['edit', 'update']]);

        if (config('twill.enabled.users-image')) {
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
                'groups' => [
                    'title' => 'Groups',
                    'module' => true,
                    'can' => 'edit-user-groups',
                ],
                'roles' => [
                    'title' => 'Roles',
                    'module' => true,
                    'can' => 'edit-user-role',
                ],
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
        ];
    }

    protected function formData($request)
    {
        $user = Auth::guard('twill_users')->user();
        $with2faSettings = config('twill.enabled.users-2fa') && $user->id == request('user');

        if ($with2faSettings) {
            $google2fa = new Google2FA();

            if (is_null($user->google_2fa_secret)) {
                $secret = $google2fa->generateSecretKey();
                $user->google_2fa_secret = \Crypt::encrypt($secret);
                $user->save();
            }

            $qrCode = $google2fa->getQRCodeInline(
                config('app.name'),
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
                'groups' => [
                    'title' => 'Groups',
                    'module' => true,
                    'can' => 'edit-user-groups',
                ],
                'roles' => [
                    'title' => 'Roles',
                    'module' => true,
                    'can' => 'edit-user-role',
                ],
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
            'permissionModules' => Permission::permissionableParentModuleItems(),
            'groupPermissionMapping' => $this->getGroupPermissionMapping(),
            'with2faSettings' => $with2faSettings,
            'qrCode' => $qrCode ?? null,
        ];
    }

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

    protected function getIndexOption($option, $item = null)
    {
        if (in_array($option, ['publish', 'bulkEdit', 'create'])) {
            return $this->user->can('edit-users');
        }

        return parent::getIndexOption($option);
    }

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
}
