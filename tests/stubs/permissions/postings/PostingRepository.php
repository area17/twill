<?php

namespace App\Repositories;

use A17\Twill\Repositories\ModuleRepository;
use App\Models\Posting;

class PostingRepository extends ModuleRepository
{
    public function __construct(Posting $model)
    {
        $this->model = $model;
    }
}
