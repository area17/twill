<?php

namespace App\Models;

use A17\Twill\Models\Model;

class ClientProject extends Model
{
    protected $fillable = [
        'published',
        'title',
        'description',
        'client_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function applications()
    {
        return $this->hasMany(ClientProjectApplication::class);
    }

}
