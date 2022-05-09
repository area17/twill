<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DisciplineResource;
use App\Repositories\DisciplineRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DisciplineController
{
    /**
     * @var DisciplineRepository
     */
    private DisciplineRepository $repository;

    public function __construct(DisciplineRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $with = ['works' => function ($query) {
            $query->take(4);
        },'works.medias', 'slugs'];
        $scopes = ['published' => true];
        $orders = ['title' => 'asc'];

        $disciplines = $this->repository->getWithCount($with, ['works'], $scopes, $orders, -1);

        return DisciplineResource::collection($disciplines);
    }
}
