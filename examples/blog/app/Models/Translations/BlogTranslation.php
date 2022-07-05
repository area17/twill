<?php

namespace App\Models\Translations;

use A17\Twill\Models\Model;
use App\Models\Blog;

class BlogTranslation extends Model
{
    protected $baseModuleModel = Blog::class;
}
