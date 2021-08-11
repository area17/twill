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
        'writer_id',
    ];

    public function writer()
    {
        return $this->belongsTo(Writer::class);
    }
}
