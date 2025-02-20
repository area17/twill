<?php

namespace App\Models;

use A17\Twill\Models\Model;

class Profile extends Model
{
    protected $fillable = [
        'published',
        'name',
        'description',
        'is_vip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
