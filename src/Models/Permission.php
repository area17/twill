<?php

namespace A17\Twill\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Permission extends BaseModel
{
    protected $fillable = [
        'guard_name',
        'display_name',
        'permissionable_type',
        'permissionable_id',
    ];

    public function permissionable()
    {
        return $this->morphTo();
    }
}
