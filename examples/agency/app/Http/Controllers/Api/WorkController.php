<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\WorkResource;
use App\Repositories\WorkRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class WorkController
{
    /**
     * @var WorkRepository
     */
    private WorkRepository $repository;

    /**
     * WorkController constructor.
     * @param WorkRepository $workRepository
     */
    public function __construct(WorkRepository $workRepository)
    {
        $this->repository = $workRepository;
    }

    /**
     * @return AnonymousResourceCollection
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index()
    {
        [$perPage, $order, $orders, $scopes] = $this->getParams();

        $works = $this->repository->getWorks(['medias', 'slugs'], $scopes, $orders, [], $perPage, true);

        return WorkResource::collection($works->appends(['perPage' => $perPage, 'order' => $order]));
    }

    /**
     * @param $slug
     * @return WorkResource
     */
    public function show($slug)
    {
        $work = $this->repository->forSlug($slug, ['blocks', 'medias']);

        return new WorkResource($work);
    }

    /**
     * @param $slug
     * @return AnonymousResourceCollection
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function sectorWorks($slug)
    {
        [$perPage, $order, $orders, $scopes] = $this->getParams();

        $relation = [
            'name' => 'sectors',
            'slug' => $slug
        ];

        $works = $this->repository->getWorks(['medias', 'slugs'], $scopes, $orders, $relation, $perPage, true);

        return WorkResource::collection($works->appends(['perPage' => $perPage, 'order' => $order]));
    }

    /**
     * @param $slug
     * @return AnonymousResourceCollection
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function disciplineWorks($slug)
    {
        [$perPage, $order, $orders, $scopes] = $this->getParams();

        $relation = [
            'name' => 'disciplines',
            'slug' => $slug
        ];

        $works = $this->repository->getWorks(['medias', 'slugs'], $scopes, $orders, $relation, $perPage, true);

        return WorkResource::collection($works->appends(['perPage' => $perPage, 'order' => $order]));
    }

    /**
     * @param array $additionalScopes
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getParams($additionalScopes = [])
    {
        $perPage = request()->get('perPage', 20);
        $order = request()->get('order', 'default');

        if ($order === 'abc') {
            $orders = ['title' => 'asc'];
        } else {
            $orders = ['created_at' => 'desc'];
        }

        $scopes = ['published' => true] + $additionalScopes;

        return [$perPage, $order, $orders, $scopes];
    }
}
