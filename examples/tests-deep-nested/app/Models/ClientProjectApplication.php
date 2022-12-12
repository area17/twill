<?php

namespace App\Models;


use A17\Twill\Models\Model;

class ClientProjectApplication extends Model
{
    protected $fillable = [
        'published',
        'title',
        'description',
        'client_project_id'
    ];

    public function project()
    {
        return $this->belongsTo(ClientProject::class);
    }
}
