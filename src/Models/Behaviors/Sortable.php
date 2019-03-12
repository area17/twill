<?php

namespace Sb4yd3e\Twill\Models\Behaviors;

interface Sortable
{
    public function scopeOrdered($query);

    public static function setNewOrder($ids, $startOrder = 1);
}
