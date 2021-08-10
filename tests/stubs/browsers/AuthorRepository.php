<?php

namespace App\Repositories;

use A17\Twill\Repositories\ModuleRepository;
use App\Models\Author;

class AuthorRepository extends ModuleRepository
{
    public function __construct(Author $model)
    {
        $this->model = $model;
    }
}
