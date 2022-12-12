<?php

namespace App\Repositories;


use A17\Twill\Repositories\ModuleRepository;
use App\Models\ClientProject;

class ClientProjectRepository extends ModuleRepository
{
    

    public function __construct(ClientProject $model)
    {
        $this->model = $model;
    }
}
