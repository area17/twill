<?php

namespace App\Twill\Capsules\Homepages\Repositories;

use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Twill\Capsules\Homepages\Models\Homepage;

class HomepageRepository extends ModuleRepository
{
    use HandleBlocks, HandleTranslations, HandleMedias, HandleFiles, HandleRevisions;

    public function __construct(Homepage $model)
    {
        $this->model = $model;
    }
}
