<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleNesting;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Node;

class NodeRepository extends ModuleRepository
{
    use HandleNesting;

    public function __construct(Node $model)
    {
        $this->model = $model;
    }
}
