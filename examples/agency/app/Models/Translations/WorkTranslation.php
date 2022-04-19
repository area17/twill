<?php

namespace App\Models\Translations;

use A17\Twill\Models\Model;
use App\Models\Work;

class WorkTranslation extends Model
{
    protected $baseModuleModel = Work::class;
}
