<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\AppSetting;
use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleTranslations;

class AppSettingsRepository extends ModuleRepository
{
    use HandleBlocks;
    use HandleTranslations;

    public function __construct(AppSetting $model)
    {
        $this->model = $model;
    }
}
