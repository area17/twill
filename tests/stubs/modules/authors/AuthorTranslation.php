<?php

namespace App\Models\Translations;

use App\Models\Author;
use A17\Twill\Models\Model;

class AuthorTranslation extends Model
{
    protected $fillable = ['name', 'description', 'active', 'locale'];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
