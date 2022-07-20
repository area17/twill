<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Behaviors\HasFiles;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\HasPresenter;
use A17\Twill\Models\Behaviors\HasRelated;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;
use App\Models\Slugs\AuthorSlug;
use App\TestPresenter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model implements Sortable
{
    use HasBlocks;
    use HasTranslation;
    use HasSlug;
    use HasMedias;
    use HasFiles;
    use HasRevisions;
    use HasRelated;
    use HasPresenter;
    use HasPosition;

    public $presenterAdmin = TestPresenter::class;

    protected $fillable = [
        'published',
        'name',
        'description',
        'bio',
        'birthday',
        'year',
        'featured',
        'position',
        'public',
        'category_id',
        'publish_start_date',
        'publish_end_date',
    ];

    protected $casts = [
        'featured' => 'boolean',
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
        'empty' => [
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
        ],
    ];

    public function slugs(): HasMany
    {
        return $this->hasMany(AuthorSlug::class);
    }

    /**
     * The main category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}
