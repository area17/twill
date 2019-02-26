<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Permission;
use A17\Twill\Models\Role;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

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

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->removeMiddleware('can:edit');
        $this->removeMiddleware('can:publish');

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
        $modules_items = Permission::permissionable_modules()->mapWithKeys(function ($module) {
            return [$module => getRepositoryByModuleName($module)->get()];
        });

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
                    'can' => 'edit-users',
                ],
                'roles' => [
                    'title' => 'Roles',
                    'module' => true,
                    'can' => 'edit-users',
                ],
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
            'permission_modules' => $modules_items,
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

    protected function getIndexOption($option, $item = null)
    {
        if (in_array($option, ['publish', 'bulkEdit', 'create'])) {
            return $this->user->can('edit-users');
        }

        return parent::getIndexOption($option);
    }

    protected function indexItemData($item)
    {
        $canEdit = $this->user->can('edit-users') || $this->user->id === $item->id;

        return ['edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null];
    }
}
