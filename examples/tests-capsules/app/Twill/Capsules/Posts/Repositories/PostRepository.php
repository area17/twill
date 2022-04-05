<?php

namespace App\Twill\Capsules\Posts\Repositories;

use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Twill\Capsules\Posts\Models\Post;

class PostRepository extends ModuleRepository
{
    use HandleBlocks,
        HandleTranslations,
        HandleSlugs,
        HandleMedias,
        HandleFiles,
        HandleRevisions;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }
}
