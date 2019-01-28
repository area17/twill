<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Enums\UserRole;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class GroupController extends ModuleController
{
    protected $namespace = 'A17\Twill';

    protected $moduleName = 'groups';

    protected $indexWith = ['medias'];

    protected $defaultOrders = ['name' => 'asc'];

    protected $defaultFilters = [
        'search' => 'search',
    ];

    protected $indexOptions = [
        'permalink' => false,
    ];

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->removeMiddleware('can:edit');
        $this->removeMiddleware('can:publish');
        $this->middleware('can:edit-user,user', ['only' => ['store', 'edit', 'update']]);
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
            'primary_navigation' => [
                'users' => [
                    'title' => 'Users',
                    'raw' => true,
                    'route' => 'users'
                ],
                'groups' => [
                    'title' => 'Groups',
                    'raw' => true,
                    'route' => 'groups'
                ]
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
        ];
    }

    protected function formData($request)
    {
        return [
            'roleList' => collect(UserRole::toArray()),
            'primary_navigation' => [
                'users' => [
                    'title' => 'Users',
                    'raw' => true,
                    'route' => 'users'
                ],
                'groups' => [
                    'title' => 'Groups',
                    'raw' => true,
                    'route' => 'groups'
                ]
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
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
        if (in_array($option, ['publish', 'bulkEdit'])) {
            return auth('twill_users')->user()->can('edit-user-role');
        }

        return parent::getIndexOption($option);
    }

    protected function indexItemData($item)
    {
        $canEdit = auth('twill_users')->user()->can('edit-user-role') || auth('twill_users')->user()->id === $item->id;

        return ['edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null];
    }
}
