<?php

namespace App\Repositories;


use A17\Twill\Repositories\ModuleRepository;
use App\Models\Client;

class ClientRepository extends ModuleRepository
{
    

    public function __construct(Client $model)
    {
        $this->model = $model;
    }
}
