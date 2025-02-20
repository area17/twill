<?php

namespace App\Models\Translations;

use A17\Twill\Models\Model;
use App\Models\Project;

class ProjectTranslation extends Model
{
    protected $baseModuleModel = Project::class;
}
