<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Writer;

class WriterRepository extends ModuleRepository
{
    use HandleRevisions;

    protected $browsers = ['bios'];

    public function __construct(Writer $model)
    {
        $this->model = $model;
    }
}
