<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\RevisionLimited;

class RevisionLimitedRepository extends ModuleRepository
{
    use HandleRevisions;

    public function __construct(RevisionLimited $model)
    {
        $this->model = $model;
    }
}
