<?php


namespace App\Http\Controllers\Api;


use App\Http\Resources\SectorResource;
use App\Repositories\SectorRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;

class SectorController
{
    /**
     * @var SectorRepository
     */
    private SectorRepository $repository;

    public function __construct(SectorRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $scopes = ['published' => true];
        $orders = ['title' => 'asc'];

        $sectors = $this->repository->getWithCount(['works.medias', 'slugs'], ['works'], $scopes, $orders, -1)
            ->map(function ($sector) {
                $works = $sector->works;
                $images = [];
                foreach ($works as $work) {
                    if (! empty($imgs = $work->imagesAsArraysWithCrops('cover'))) {
                        array_push($images, $imgs);
                    }
                }
                $sector->images = Arr::collapse($images);
                return $sector;
            });

        return SectorResource::collection($sectors);
    }
}
