<?php

namespace App\Models\Translations;

use A17\Twill\Models\Model;
use App\Models\Author;

class AuthorTranslation extends Model
{
    protected $fillable = ['name', 'description', 'bio', 'active', 'locale'];

    protected $casts = ['active' => 'boolean'];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
