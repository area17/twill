<?php

namespace A17\Twill\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Permission extends BaseModel
{
    public function permissionable() {
        return $this->morphTo();
    }
}
