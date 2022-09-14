<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Model;

class RevisionLimited extends Model
{
    use HasRevisions;

    public int $limitRevisions = 5;

    protected $fillable = [
        'published',
        'title',
        'description',
    ];

}
