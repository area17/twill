<?php

namespace App\Models\Translations;

use App\Models\Author;
use A17\Twill\Models\Model;

class AuthorTranslation extends Model
{
    protected $fillable = ['name', 'description', 'bio', 'active', 'locale'];

    protected $casts = ['active' => 'boolean'];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
