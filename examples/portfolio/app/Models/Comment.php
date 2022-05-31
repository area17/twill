<?php

namespace App\Models;

use A17\Twill\Models\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $fillable = [
        'published',
        'title',
        'comment',
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
