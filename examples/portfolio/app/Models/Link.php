<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;

class Link extends Model implements Sortable
{
    use HasPosition;

    protected $fillable = [
        'published',
        'title',
        'description',
        'position',
        'project_id',
        'url'
    ];

}
