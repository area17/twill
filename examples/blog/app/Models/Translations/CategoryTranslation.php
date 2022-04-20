<?php

namespace App\Models\Translations;

use A17\Twill\Models\Model;
use App\Models\Category;

class CategoryTranslation extends Model
{
    protected $baseModuleModel = Category::class;
}
