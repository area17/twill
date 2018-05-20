<?php

namespace A17\Twill\Models\Behaviors;

interface Sortable
{
    public function scopeOrdered($query);

    public static function setNewOrder($ids, $startOrder = 1);
}
