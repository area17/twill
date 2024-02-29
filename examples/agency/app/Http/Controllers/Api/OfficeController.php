<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\OfficeResource;
use App\Repositories\OfficeRepository;

class OfficeController
{
    /**
     * @var OfficeRepository
     */
    private OfficeRepository $repository;

    public function __construct(OfficeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $scopes = ['published' => true];

        $offices = $this->repository->get(['medias'], $scopes, [], -1);

        return OfficeResource::collection($offices);
    }
}
