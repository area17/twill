<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use App\Models\Sector;

class SectorRepository extends BaseRepository
{
    use HandleTranslations, HandleSlugs;

    public function __construct(Sector $model)
    {
        $this->model = $model;
    }
}
