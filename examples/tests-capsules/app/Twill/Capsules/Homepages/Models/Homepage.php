<?php

namespace App\Twill\Capsules\Homepages\Models;

use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasFiles;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Model;

class Homepage extends Model 
{
    use HasBlocks, HasTranslation, HasMedias, HasFiles, HasRevisions;

    protected $fillable = [
        'published',
        'title',
        'description',
    ];
    
    public $translatedAttributes = [
        'title',
        'description',
        'active',
    ];
    
}
