<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Model;

class Writer extends Model
{
    use HasRevisions;

    protected $fillable = [
        'published',
        'title',
        'description',
    ];

    public function bios()
    {
        return $this->hasMany(Bio::class);
    }
}
