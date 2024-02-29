<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Model;

class About extends Model
{
    use HasTranslation, HasRevisions;

    protected $fillable = [
        'published',
        'title',
        'description',
    ];

    public $translatedAttributes = [
        'title',
        'text',
        'tagline',
        'active',
    ];

}
