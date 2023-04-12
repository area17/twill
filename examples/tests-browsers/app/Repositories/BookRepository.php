<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Book;

class BookRepository extends ModuleRepository
{
    use HandleRevisions;

    protected $relatedBrowsers = ['writers', 'books'];

    public function __construct(Book $model)
    {
        $this->model = $model;
    }
}
