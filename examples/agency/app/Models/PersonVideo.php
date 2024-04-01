<?php

namespace App\Models;

use A17\Twill\API\Models\Traits\HasMediables;
use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;

class PersonVideo extends Model implements Sortable
{
    use HasTranslation, HasSlug, HasMedias, HasPosition, HasMediables;

    protected $fillable = [
        'published',
        'position',
        'date',
        'video_url',
        'person_id'
    ];

    public $translatedAttributes = [
        'title',
        'active',
    ];

    public $slugAttributes = [
        'title',
    ];

    public $mediasParams = [
        'video' => [
            'default' => [
                [
                    'name' => 'default',
                    'ratio' => 16 / 9,
                ],
            ],
            'mobile' => [
                [
                    'name' => 'mobile',
                    'ratio' => 1,
                ],
            ],
            'flexible' => [
                [
                    'name' => 'free',
                    'ratio' => 0,
                ],
                [
                    'name' => 'landscape',
                    'ratio' => 16 / 9,
                ],
                [
                    'name' => 'portrait',
                    'ratio' => 3 / 5,
                ],
            ],
        ],
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function getFormattedDateAttribute()
    {
        return $this->date->format('M, Y') ;
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
