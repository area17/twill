<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Block;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface as Logger;

class BlockRepository extends ModuleRepository
{
    use HandleMedias, HandleFiles;

    /**
     * @param DB $db
     * @param Logger $logger
     * @param Application $app
     * @param Config $config
     * @param Block $model
     */
    public function __construct(DB $db, Logger $logger, Application $app, Config $config, Block $model)
    {
        parent::__construct($db, $logger, $app, $config);

        $this->model = $model;
    }

    public function getCrops($role)
    {
        return $this->config->get('twill.block_editor.crops')[$role];
    }

    public function afterDelete($object)
    {
        $object->medias()->sync([]);
        $object->files()->sync([]);
    }

    public function buildFromCmsArray($block, $repeater = false)
    {
        $blocksFromConfig = $this->config->get('twill.block_editor.' . ($repeater ? 'repeaters' : 'blocks'));

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
