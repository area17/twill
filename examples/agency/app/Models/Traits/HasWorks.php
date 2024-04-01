<?php

namespace App\Models\Traits;

use App\Models\Work;

trait HasWorks
{
    public function works()
    {
        return $this->belongsToMany(Work::class);
    }
}
