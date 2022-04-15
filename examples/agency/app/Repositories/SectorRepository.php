<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Sector;

class SectorRepository extends ModuleRepository
{
    use HandleTranslations, HandleSlugs;

    public function __construct(Sector $model)
    {
        $this->model = $model;
    }
}
