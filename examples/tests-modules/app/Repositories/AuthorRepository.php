<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleTags;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Author;

class AuthorRepository extends ModuleRepository
{
    use HandleBlocks;
    use HandleFiles;
    use HandleMedias;
    use HandleRevisions;
    use HandleSlugs;
    use HandleTags;
    use HandleTranslations;

    public function __construct(Author $model)
    {
        $this->model = $model;
    }
}
