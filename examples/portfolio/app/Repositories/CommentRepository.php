<?php

namespace App\Repositories;


use A17\Twill\Repositories\ModuleRepository;
use App\Models\Comment;

class CommentRepository extends ModuleRepository
{
    

    public function __construct(Comment $model)
    {
        $this->model = $model;
    }
}
