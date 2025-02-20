<?php

namespace App\Repositories;


use A17\Twill\Repositories\ModuleRepository;
use App\Models\ClientProjectApplication;

class ClientProjectApplicationRepository extends ModuleRepository
{
    

    public function __construct(ClientProjectApplication $model)
    {
        $this->model = $model;
    }
}
