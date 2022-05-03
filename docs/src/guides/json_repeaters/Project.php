<?php

namespace App\Models;

use A17\Twill\Models\Model;

class Project extends Model
{
    protected $fillable = [
        'published',
        'title',
        'description',
        'tasks'
    ];

    protected $casts = [
        'tasks' => 'array'
    ];
}
