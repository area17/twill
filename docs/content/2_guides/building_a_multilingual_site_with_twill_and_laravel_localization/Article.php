<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Model;
use App\Repositories\ArticleRepository;
use Mcamara\LaravelLocalization\Interfaces\LocalizedUrlRoutable;

class Article extends Model implements LocalizedUrlRoutable
{
    use HasTranslation;
    use HasSlug;

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

    public $slugAttributes = [
        'title',
    ];

    public function resolveRouteBinding($slug, $field = null)
    {
        $article = app(ArticleRepository::class)->forSlug($slug);

        abort_if(! $article, 404);

        return $article;
    }

    // #region routekey
    public function getLocalizedRouteKey($locale)
    {
        return $this->getSlug($locale);
    }

    // #endregion routekey
}
