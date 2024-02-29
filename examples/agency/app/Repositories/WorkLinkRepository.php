<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\WorkLink;

class WorkLinkRepository extends ModuleRepository
{
    use HandleTranslations;

    public function __construct(WorkLink $model)
    {
        $this->model = $model;
    }
}
