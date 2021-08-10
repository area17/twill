<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Model;

class Article extends Model
{
    use HasRevisions;

    protected $fillable = [
        'published',
        'title',
        'description',
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }
}
