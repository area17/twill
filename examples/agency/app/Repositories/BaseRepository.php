<?php


namespace App\Repositories;

use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Repositories\ModuleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseRepository extends ModuleRepository
{
    /**
     * @param array $with
     * @param array $withCount
     * @param array $scopes
     * @param array $orders
     * @param int $perPage
     * @param false $forcePagination
     * @return LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getWithCount($with = [], $withCount = [], $scopes = [], $orders = [], $perPage = 20, $forcePagination = false)
    {
        $query = $this->model->with($with);

        $query = $this->filter($query, $scopes);
        $query = $this->order($query, $orders);
        $query->withCount($withCount);

        if (! $forcePagination && $this->model instanceof Sortable) {
            return $query->ordered()->get();
        }

        if ($perPage == -1) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }
}
