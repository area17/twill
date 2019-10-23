<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\UserOauth;

trait HasOauth
{

    public function providers()
    {

        return $this->hasMany(UserOauth::class, 'user_id')

    }

}
