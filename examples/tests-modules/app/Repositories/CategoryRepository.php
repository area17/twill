<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;

class CategoryRepository extends ModuleRepository
{
    use HandleBlocks, HandleTranslations, HandleSlugs, HandleRevisions;

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
