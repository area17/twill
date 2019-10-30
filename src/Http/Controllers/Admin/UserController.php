<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Enums\UserRole;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
    protected $indexColumns;

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

        $this->authFactory = $authFactory;
        $this->config = $config;

        $this->removeMiddleware('can:edit');
        $this->removeMiddleware('can:delete');
        $this->removeMiddleware('can:publish');
        $this->middleware('can:manage-users', ['only' => ['index']]);
        $this->middleware('can:edit-user,user', ['only' => ['store', 'edit', 'update', 'destroy', 'bulkDelete', 'restore', 'bulkRestore']]);
        $this->middleware('can:publish-user', ['only' => ['publish']]);

        $this->indexColumns = [
            'name' => [
                'title' => __('twill::lang.user-management.name'),
                'field' => 'name',
            ],
            'email' => [
                'title' => __('twill::lang.user-management.email'),
                'field' => 'email',
                'sort' => true,
            ],
            'role_value' => [
                'title' => __('twill::lang.user-management.role'),
                'field' => 'role_value',
                'sort' => true,
                'sortKey' => 'role',
            ],
        ];

        if ($this->config->get('twill.enabled.users-image')) {
            $this->indexColumns = [
                'image' => [
                    'title' => '画像',
                    'thumb' => true,
                    'variant' => [
                        'role' => 'profile',
                        'crop' => 'default',
                    ],
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
            'defaultFilterSlug' => 'published',
            'create' => $this->getIndexOption('create') && $this->authFactory->guard('twill_users')->user()->can('manage-users'),
            'roleList' => Collection::make(UserRole::toArray()),
            'single_primary_nav' => [
                'users' => [
                    'title' => __('twill::lang.user-management.users'),
                    'module' => true,
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
        $with2faSettings = $this->config->get('twill.enabled.users-2fa') && $user->id == $request->route('user');

        if ($with2faSettings) {
            $user->generate2faSecretKey();

            $qrCode = $user->get2faQrCode();
        }

        return [
            'roleList' => Collection::make(UserRole::toArray()),
            'single_primary_nav' => [
                'users' => [
                    'title' => __('twill::lang.user-management.users'),
                    'module' => true,
                ],
            ],
            'customPublishedLabel' => __('twill::lang.user-management.enabled'),
            'customDraftLabel' => __('twill::lang.user-management.disabled'),
            'with2faSettings' => $with2faSettings,
            'qrCode' => $qrCode ?? null,
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
            'name' => __('twill::lang.user-management.active'),
            'slug' => 'published',
            'number' => $this->repository->getCountByStatusSlug('published'),
        ], [
            'name' => __('twill::lang.user-management.disabled'),
            'slug' => 'draft',
            'number' => $this->repository->getCountByStatusSlug('draft'),
        ]);

        if ($this->getIndexOption('restore')) {
            array_push($statusFilters, [
                'name' => __('twill::lang.user-management.trash'),
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
    protected function getIndexOption($option)
    {
        if (in_array($option, ['publish', 'delete', 'restore'])) {
            return $this->authFactory->guard('twill_users')->user()->can('manage-users');
        }

        return parent::getIndexOption($option);
    }

    /**
     * @param \A17\Twill\Models\Model $item
     * @return array
     */
    protected function indexItemData($item)
    {

        $user = $this->authFactory->guard('twill_users')->user();
        $canEdit = $user->can('manage-users') || $user->id === $item->id;
        return [
            'edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null,
        ];
    }
}
