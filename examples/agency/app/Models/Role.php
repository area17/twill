<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;

class Role extends Model implements Sortable
{
    use HasTranslation, HasSlug, HasPosition;

    protected $fillable = [
        'published',
        'title',
        'position',
    ];

    public $translatedAttributes = [
        'title',
        'active',
    ];

    public $slugAttributes = [
        'title',
    ];

}
