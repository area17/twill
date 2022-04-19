<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;
use DateTimeZone;

class OfficeController extends BaseModuleController
{
    protected $moduleName = 'offices';

    protected $indexOptions = [
        'permalink' => false,
    ];

    protected function formData($request)
    {
        $timezones = array_map(function ($timezone) {
            return ['value' => $timezone, 'label' => $timezone];
        }, DateTimeZone::listIdentifiers());

        return [
            'timezones' => $timezones
        ];
    }

    protected $indexColumns = [
        'image' => [
            'thumb' => true,
            'variant' => [
                'role' => 'cover',
                'crop' => 'default'
            ]
        ],
        'full_name' => [
            'title' => 'Title',
            'field' => 'title',
            'sort' => true
        ]
    ];
}
