<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\RevisionLimitedContent;

class RevisionLimitedRepository extends ModuleRepository
{
    use HandleRevisions;

    public function __construct(RevisionLimitedContent $model)
    {
        $this->model = $model;
    }
}
