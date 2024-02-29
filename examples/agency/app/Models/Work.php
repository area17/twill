<?php

namespace App\Models;

use A17\Twill\API\Models\Traits\HasFileables;
use A17\Twill\API\Models\Traits\HasMediables;
use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Behaviors\HasFiles;
use A17\Twill\Models\Behaviors\HasRelated;
use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;

class Work extends Model implements Sortable
{
    use HasBlocks, HasTranslation, HasSlug, HasMedias, HasRevisions, HasPosition, HasFiles, HasRelated, HasMediables, HasFileables;

    protected $fillable = [
        'published',
        'position',
        'video_url',
        'autoplay',
        'autoloop',
        'publish_start_date',
        'client_name',
        'year'
    ];

    public $translatedAttributes = [
        'title',
        'subtitle',
        'description',
        'case_study_text',
        'active',
    ];

    public $slugAttributes = [
        'title',
    ];

    public $mediasParams = [
        'cover' => [
            'default' => [
                [
                    'name' => 'default',
                    'ratio' => 8 / 5,
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
        'homepage_slideshow' => [
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
        ],
        'feature_grid' => [
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
        ],
        'image' => [
            'desktop' => [
                [
                    'name' => 'desktop',
                    'ratio' => 1,
                ],
            ],
        ]
    ];

    public $filesParams = ['video'];

    protected $casts = [
        'autoplay' => 'boolean',
        'autoloop' => 'boolean',
        'published' => 'boolean'
    ];

    public function sectors()
    {
        return $this->belongsToMany(Sector::class);
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class);
    }

    public function workLinks()
    {
        return $this->hasMany(WorkLink::class);
    }

    public function people()
    {
        return $this->belongsToMany(Person::class);
    }
}
