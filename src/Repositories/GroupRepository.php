<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Group;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use DB;

class GroupRepository extends ModuleRepository
{
    use HandleMedias;

    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    public function getCountForPublished()
    {
        return $this->model->published()->count();
    }

    public function getCountForDraft()
    {
        return $this->model->draft()->count();
    }

    public function getCountForTrash()
    {
        return $this->model->onlyTrashed()->count();
    }
}
