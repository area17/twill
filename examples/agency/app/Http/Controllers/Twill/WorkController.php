<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;
use App\Repositories\DisciplineRepository;
use App\Repositories\SectorRepository;

class WorkController extends BaseModuleController
{
    protected $moduleName = 'works';

    protected $indexOptions = [
    ];

    protected function formData($request)
    {
        $years = array_map(function ($year) {
            return [
                'value' => $year,
                'label' => $year
            ];
        },array_reverse(range(1950, date('Y'))));

        return [
            'sectors' => app()->make(SectorRepository::class)->listAll(),
            'years' => $years,
            'disciplines' => app()->make(DisciplineRepository::class)->listAll()
        ];
    }
}
