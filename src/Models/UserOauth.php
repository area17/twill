<?php

namespace A17\Twill\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class UserOauth extends BaseModel
{
    protected $fillable = [
        'token',
        'provider',
        'avatar',
        'oauth_id',
        'user_id',
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('twill.users_oauth_table', 'twill_users_oauth');

        parent::__construct($attributes);
    }

    public function user()
    {
        $this->belongsTo(twillModel('user'), 'user_id');
    }
}
