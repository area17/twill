<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Partner;

class PartnerRepository extends ModuleRepository
{
    use HandleTranslations, HandleSlugs, HandleMedias, HandleRevisions;

    public function __construct(Partner $model)
    {
        $this->model = $model;
    }
}
