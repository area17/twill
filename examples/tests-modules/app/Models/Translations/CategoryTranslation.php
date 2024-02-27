<?php

namespace App\Models\Translations;

use A17\Twill\Models\Model;
use App\Models\Category;

class CategoryTranslation extends Model
{
    protected $fillable = ['title', 'description', 'active', 'locale'];

    protected $casts = ['active' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
