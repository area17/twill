<?php

namespace App\Models\Translations;

use A17\Twill\Models\Model;
use App\Models\Person;

class PersonTranslation extends Model
{
    protected $baseModuleModel = Person::class;
}
