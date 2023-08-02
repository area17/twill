<?php

namespace App\Repositories;


use A17\Twill\Repositories\ModuleRepository;
use App\Models\Link;

class LinkRepository extends ModuleRepository
{
    

    public function __construct(Link $model)
    {
        $this->model = $model;
    }
}
