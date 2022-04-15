<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Model;

class Sector extends Model
{
    use HasTranslation, HasSlug;

    protected $fillable = [
        'published',
    ];

    public $translatedAttributes = [
        'title',
        'active',
    ];

    public $slugAttributes = [
        'title',
    ];

}
