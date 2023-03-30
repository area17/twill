<?php

namespace A17\Twill\Models\Behaviors;

interface Sortable
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query);

    /**
     * @param array $ids
     * @param int $startOrder
     * @return void
     */
    public static function setNewOrder($ids, $startOrder = 1);
}
