<?php

namespace A17\CmsToolkit\Repositories;

use A17\CmsToolkit\Models\Block;
use A17\CmsToolkit\Repositories\Behaviors\HandleMedias;

class BlockRepository extends ModuleRepository
{
    use HandleMedias;

    public function __construct(Block $model)
    {
        $this->model = $model;
    }

    public function getCrops($role)
    {
        return config('cms-toolkit.block_editor.crops')[$role];
    }

    public function afterDelete($object)
    {
        $object->medias()->sync([]);
    }
}
