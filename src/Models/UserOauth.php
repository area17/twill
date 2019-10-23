<?php

namespace A17\Twill\Models;

use A17\Twill\Models\User;

class UserOauth extends Model
{

    protected $fillable = [
        'token',
        'provider',
        'oauth_id',
        'user_id',
    ];

    public function user()
    {

        $this->belongsTo(User::class, 'user_id')

    }

}
