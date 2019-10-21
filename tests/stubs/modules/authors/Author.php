<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasFiles;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;
use App\Models\Slugs\AuthorSlug;
use App\Models\Revisions\AuthorRevision;

class Author extends Model implements Sortable
{
    use HasBlocks, HasTranslation, HasSlug, HasMedias, HasFiles, HasRevisions;

    protected $fillable = [
        'published',
        'name',
        'description',
        'bio',
        'birthday',
        // 'public',
        // 'featured',
        // 'publish_start_date',
        // 'publish_end_date',
    ];

    // uncomment and modify this as needed if you use the HasTranslation trait
    public $translatedAttributes = ['name', 'description', 'active', 'bio'];

    // uncomment and modify this as needed if you use the HasSlug trait
    public $slugAttributes = ['name'];

    // add checkbox fields names here (published toggle is itself a checkbox)
    public $checkboxes = ['published'];

    // uncomment and modify this as needed if you use the HasMedias trait
    public $mediasParams = [
        'avatar' => [
            'default' => [
                [
                    'name' => 'landscape',
                    'ratio' => 16 / 9,
                ],
                [
                    'name' => 'portrait',
                    'ratio' => 3 / 4,
                ],
            ],
            'mobile' => [
                [
                    'name' => 'mobile',
                    'ratio' => 1,
                ],
            ],
        ],
    ];

    public function slugs()
    {
        return $this->hasMany(AuthorSlug::class);
    }

    public function revisions()
    {
        return $this->hasMany(AuthorRevision::class);
    }

    public function scopeOrdered($query)
    {
        // TODO: Implement scopeOrdered() method.
    }

    public static function setNewOrder($ids, $startOrder = 1)
    {
        // TODO: Implement setNewOrder() method.
    }
}
