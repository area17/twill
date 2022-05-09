<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use App\Models\Discipline;

class DisciplineRepository extends BaseRepository
{
    use HandleTranslations, HandleSlugs;

    public function __construct(Discipline $model)
    {
        $this->model = $model;
    }
}
