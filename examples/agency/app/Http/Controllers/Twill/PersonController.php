<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;
use App\Models\Office;
use App\Models\Role;

class PersonController extends BaseModuleController
{
    protected $moduleName = 'people';

    protected $indexOptions = [
    ];

    protected $titleFormKey = 'full_name';
    protected $titleColumnKey = 'full_name';

    protected $indexColumns = [
        'image' => [
            'thumb' => true,
            'variant' => [
                'role' => 'main',
                'crop' => 'default'
            ]
        ],
        'full_name' => [
            'title' => 'Full Name',
            'field' => 'full_name',
            'sort' => true
        ],
        'role' => [
            'title' => 'Role',
            'field' => 'role_name',
            'sort' => true
        ],
        'office' => [
            'title' => 'Office',
            'field' => 'office_name',
            'sort' => true
        ]
    ];

    protected function formData($request)
    {
        $offices = array_map(function ($office) {
            return [
                'value' => $office['id'],
                'label' => $office['title']
            ];
        }, Office::published()->get()->toArray());

        $years = array_map(function ($year) {
            return [
                'value' => $year,
                'label' => $year
            ];
        },array_reverse(range(1950, date('Y'))));

        $roles = array_map(function ($role) {
            return [
                'value' => $role['id'],
                'label' => $role['title']
            ];
        }, Role::published()->get()->toArray());

        return [
            'offices' => $offices,
            'years' => $years,
            'roles' => $roles
        ];
    }
}

