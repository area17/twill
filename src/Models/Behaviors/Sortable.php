<?php

namespace A17\Twill\Models\Behaviors;

use Illuminate\Database\Eloquent\Builder;

interface Sortable
{
    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeOrdered($query);

    /**
     * @param  array  $ids
     * @param  int  $startOrder
     * @return void
     */
    public static function setNewOrder($ids, $startOrder = 1);
}
