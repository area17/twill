<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Behaviors\HasFiles;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Block;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use Illuminate\Support\Collection;

class BlockRepository extends ModuleRepository
{
    use HandleMedias, HandleFiles;

    /**
     * @param Block $model
     */
    public function __construct(Block $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $role
     * @return array
     */
    public function getCrops($role)
    {
        return config('twill.block_editor.crops')[$role];
    }

    /**
     * @param HasMedias|HasFiles $object
     * @return void
     */
    public function afterDelete($object)
    {
        $object->medias()->sync([]);
        $object->files()->sync([]);
    }

    /**
     * @param array $block
     * @param bool $repeater
     * @return array
     */
    public function buildFromCmsArray($block, $repeater = false)
    {
        $blocksFromConfig = config('twill.block_editor.' . ($repeater ? 'repeaters' : 'blocks'));

        $block['type'] = Collection::make($blocksFromConfig)->search(function ($blockConfig) use ($block) {
            return $blockConfig['component'] === $block['type'];
        });

        $block['content'] = empty($block['content']) ? new \stdClass : (object) $block['content'];

        if ($block['browsers']) {
            $browsers = Collection::make($block['browsers'])->map(function ($items) {
                return Collection::make($items)->pluck('id');
            })->toArray();

            $block['content']->browsers = $browsers;
        }

        return $block;
    }
}
