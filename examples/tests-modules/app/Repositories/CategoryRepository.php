<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends ModuleRepository
{
    use HandleBlocks, HandleRevisions, HandleSlugs, HandleTranslations;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function setNewOrder(array $ids): void
    {
        DB::transaction(function () use ($ids) {
            Category::saveTreeFromIds($ids);
        }, 3);
    }
}
