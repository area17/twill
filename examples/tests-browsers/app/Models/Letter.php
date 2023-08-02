<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasRelated;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Model;

class Letter extends Model
{
    use HasRevisions;
    use HasRelated;

    protected $fillable = [
        'published',
        'title',
        'description',
    ];

    public function writers()
    {
        return $this->belongsToMany(Writer::class);
    }
}
