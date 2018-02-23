<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use A17\CmsToolkit\Models\Enums\UserRole;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class UserController extends ModuleController
{
    protected $namespace = 'A17\CmsToolkit';

    protected $moduleName = 'users';

    protected $indexWith = ['medias'];

    protected $defaultOrders = ['name' => 'asc'];

    protected $defaultFilters = [
        'search' => '%name',
    ];
    protected $filters = [
        'fRole' => 'role',
    ];

    protected $titleColumnKey = 'name';

    protected $indexColumns = [
        'name' => [
            'title' => 'Name',
            'edit_link' => true,
            'field' => 'name',
        ],
        'email' => [
            'title' => 'Email',
            'field' => 'email',
        ],
        'role' => [
            'title' => 'Role',
            'field' => 'role_value',
        ],
    ];

    protected $indexOptions = [
        'permalink' => false,
    ];

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->removeMiddleware('can:edit');
        $this->middleware('can:edit-user,user', ['only' => ['store', 'edit', 'update']]);

        if (config('cms-toolkit.enabled.users-image')) {
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
            'create' => $this->getIndexOption('create') && auth()->user()->can('edit-user-role'),
            'roleList' => collect(UserRole::toArray()),
            'single_primary_nav' => [
                'users' => [
                    'title' => 'Users',
                    'module' => true,
                ],
            ],
        ];
    }

    protected function formData($request)
    {
        return [
            'roleList' => collect(UserRole::toArray()),
            'single_primary_nav' => [
                'users' => [
                    'title' => 'Users',
                    'module' => true,
                ],
            ],
        ];
    }

    protected function getRequestFilters()
    {
        return json_decode($this->request->get('filter'), true) ?? ['status' => 'published'];
    }

    public function getIndexTableMainFilters($items)
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

        return $statusFilters;
    }
}
