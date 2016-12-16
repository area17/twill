<?php

namespace A17\CmsToolkit\Models\Behaviors;

interface Sortable
{
    public function scopeOrdered($query);

    public static function setNewOrder($ids, $startOrder = 1);
}
