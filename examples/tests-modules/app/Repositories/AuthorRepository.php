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
    // Slugs is before translations deliberately as this used to give errors.
    use HandleSlugs;
    use HandleTranslations;
    use HandleMedias;
    use HandleFiles;
    use HandleRevisions;
    use HandleTags;

    public function __construct(Author $model)
    {
        $this->model = $model;
    }
}
