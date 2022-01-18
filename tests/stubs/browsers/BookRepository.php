<?php

namespace App\Repositories\Twill;

use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Book;

class BookRepository extends ModuleRepository
{
    use HandleRevisions;

    protected $relatedBrowsers = ['writers'];

    public function __construct(Book $model)
    {
        $this->model = $model;
    }
}
