<?php

namespace App\Repositories;

use A17\Twill\Repositories\ModuleRepository;
use App\Models\Post;

class PostRepository extends ModuleRepository
{
    public function __construct(Post $model)
    {
        $this->model = $model;
    }
}
