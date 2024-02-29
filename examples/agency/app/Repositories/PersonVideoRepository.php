<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\PersonVideo;

class PersonVideoRepository extends ModuleRepository
{
    use HandleTranslations, HandleSlugs, HandleMedias;

    public function __construct(PersonVideo $model)
    {
        $this->model = $model;
    }
}
