<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Model;

class Bio extends Model
{
    use HasRevisions;

    protected $fillable = [
        'published',
        'title',
        'description',
        'author_id',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
