<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;

class WorkLink extends Model implements Sortable
{
    use HasTranslation, HasPosition;

    protected $fillable = [
        'published',
        'url',
        'position',
        'work_id'
    ];

    public $translatedAttributes = [
        'label',
        'active',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
