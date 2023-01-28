<?php

namespace App\Models;


use A17\Twill\Models\Model;

class Client extends Model
{
    protected $fillable = [
        'published',
        'title',
        'description',
    ];


    public function projects()
    {
        return $this->hasMany(ClientProject::class);
    }
}
