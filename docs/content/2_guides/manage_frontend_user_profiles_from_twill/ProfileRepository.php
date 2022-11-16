<?php

namespace App\Repositories;

use A17\Twill\Repositories\ModuleRepository;
use App\Models\Profile;

class ProfileRepository extends ModuleRepository
{
    public function __construct(Profile $model)
    {
        $this->model = $model;
    }
}
