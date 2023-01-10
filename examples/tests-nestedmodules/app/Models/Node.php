<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasNesting;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;
use Illuminate\Support\Str;

class Node extends Model implements Sortable
{
    use HasPosition;
    use HasNesting;

    protected $fillable = [
        'published',
        'title',
        'description',
        'position',
    ];

    public function getSlug()
    {
        return Str::slug($this->title);
    }
}
