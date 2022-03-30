<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Bio;

class BioRepository extends ModuleRepository
{
    use HandleRevisions;

    protected $browsers = ['writer'];

    public function __construct(Bio $model)
    {
        $this->model = $model;
    }
}
