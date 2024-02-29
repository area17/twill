<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\About;

class AboutRepository extends ModuleRepository
{
    use HandleTranslations, HandleRevisions;

    public function __construct(About $model)
    {
        $this->model = $model;
    }
}
