<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\ContactPage;

class ContactPageRepository extends ModuleRepository
{
    use HandleBlocks, HandleFiles, HandleMedias, HandleRevisions, HandleSlugs, HandleTranslations;

    public function __construct(ContactPage $model)
    {
        $this->model = $model;
    }
}
