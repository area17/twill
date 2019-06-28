<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Enums\UserRole;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Session\Store as SessionStore;
use PragmaRX\Google2FAQRCode\Google2FA;

class UserController extends ModuleController
{
    /**
     * @var AuthManager
     */
    protected $authManager;

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
            'sortKey' => 'role',
        ],
    ];

    protected $indexOptions = [
        'permalink' => false,
    ];

    protected $fieldsPermissions = [
        'role' => 'edit-user-role',
    ];

    /**
     * UserController constructor.
     * @param Application $app
     * @param Request $request
     * @param Router $router
     * @param SessionStore $sessionStore
     * @param AuthManager $authManager
     */
    public function __construct(
        Application $app,
        Request $request,
        Router $router,
        SessionStore $sessionStore,
        AuthManager $authManager
    ) {
        parent::__construct($app, $request, $router, $sessionStore);
        $this->authManager = $authManager;
        $this->removeMiddleware('can:edit');
        $this->removeMiddleware('can:delete');
        $this->removeMiddleware('can:publish');
        $this->middleware('can:edit-user-role', ['only' => ['index']]);
        $this->middleware('can:edit-user,user', ['only' => ['store', 'edit', 'update', 'destroy', 'bulkDelete', 'restore', 'bulkRestore']]);
        $this->middleware('can:publish-user', ['only' => ['publish']]);

        if (config('twill.enabled.users-image')) {
            $this->indexColumns = [
                'image' => [
                    'title' => 'Image',
                    'thumb' => true,
                    'variant' => [
                        'role' => 'profile',
                        'crop' => 'default',
                    ],
                ],
            ] + $this->indexColumns;
        }
    }

    protected function indexData($request)
    {
        return [
            'defaultFilterSlug' => 'published',
            'create' => $this->getIndexOption('create') && auth('twill_users')->user()->can('edit-user-role'),
            'roleList' => collect(UserRole::toArray()),
            'single_primary_nav' => [
                'users' => [
                    'title' => 'Users',
                    'module' => true,
                ],
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
        ];
    }

    protected function formData($request)
    {
        $user = $this->authManager->guard('twill_users')->user();
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
            'roleList' => collect(UserRole::toArray()),
            'single_primary_nav' => [
                'users' => [
                    'title' => 'Users',
                    'module' => true,
                ],
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
            'with2faSettings' => $with2faSettings,
            'qrCode' => $qrCode ?? null,
        ];
    }

    protected function getRequestFilters()
    {
        return json_decode($this->request->get('filter'), true) ?? ['status' => 'published'];
    }

    public function getIndexTableMainFilters($items, $scopes = [])
    {
        $statusFilters = [];

        array_push($statusFilters, [
            'name' => 'Active',
            'slug' => 'published',
            'number' => $this->repository->getCountByStatusSlug('published'),
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

    protected function getIndexOption($option)
    {
        if (in_array($option, ['publish', 'delete', 'restore'])) {
            return auth('twill_users')->user()->can('edit-user-role');
        }

        return parent::getIndexOption($option);
    }

    protected function indexItemData($item)
    {
        $canEdit = auth('twill_users')->user()->can('edit-user-role') || auth('twill_users')->user()->id === $item->id;
        return [
            'edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null,
        ];
    }
}
