<?php

namespace App\Http\Controllers\Api;

use A17\Twill\Repositories\SettingRepository;

class SettingController
{
    /**
     * @var SettingRepository
     */
    private SettingRepository $repository;

    public function __construct(SettingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $settings = collect($this->repository->getFormFields())->mapWithKeys(function ($values, $column) {
            if ($column === 'medias') {
                return [$column => $values];
            }
            return [$column => $values[app()->getLocale()]];
        });

        return response()->json($settings);
    }
}
