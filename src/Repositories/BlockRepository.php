<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Behaviors\HasFiles;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Services\Blocks\Block as BlockConfig;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Collection;
use Log;
use ReflectionException;
use Schema;

class BlockRepository extends ModuleRepository
{
    use HandleMedias, HandleFiles;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $blockModel = twillModel('block');
        $this->model = new $blockModel;
        $this->config = $config;
    }

    /**
     * @param string $role
     * @return array
     */
    public function getCrops($role)
    {
        return $this->config->get('twill.block_editor.crops')[$role];
    }

    public function hydrate($object, $fields)
    {
        if (Schema::hasTable(config('twill.related_table', 'twill_related'))) {
            $relatedItems = Collection::make();

            Collection::make($fields['browsers'])->each(function ($items, $browserName) use (&$relatedItems) {
                Collection::make($items)->each(function ($item) use ($browserName, &$relatedItems) {
                    try {
                        $repository = $this->getModelRepository($item['endpointType'] ?? $browserName);
                        $relatedItems->push((object) [
                            'related' => $repository->getById($item['id']),
                            'browser_name' => $browserName,
                        ]);

                    } catch (ReflectionException $e) {
                        Log::error($e);
                    }
                });
            });

            $object->setRelation('relatedItems', $relatedItems);
        }

        return parent::hydrate($object, $fields);
    }

    /**
     * @param HasMedias|HasFiles $object
     * @return void
     */
    public function afterSave($object, $fields)
    {
        if (Schema::hasTable(config('twill.related_table', 'twill_related'))) {
            if (isset($fields['browsers'])) {
                Collection::make($fields['browsers'])->each(function ($items, $browserName) use ($object) {
                    $object->saveRelated($items, $browserName);
                });
            }
        }

        parent::afterSave($object, $fields);
    }

    public function afterDelete($object)
    {
        $object->medias()->sync([]);
        $object->files()->sync([]);

        if (Schema::hasTable(config('twill.related_table', 'twill_related'))) {
            $object->relatedItems()->delete();
        }
    }

    /**
     * @param array $block
     * @param bool $repeater
     * @return array
     */
    public function buildFromCmsArray($block, $repeater = false)
    {
        $blockInstance = BlockConfig::getForComponent($block['type'], $repeater);

        $block['type'] = $blockInstance->name;

        $block['instance'] = $blockInstance;

        $block['content'] = empty($block['content']) ? new \stdClass() : (object) $block['content'];

        if ($block['browsers']) {
            $browsers = Collection::make($block['browsers'])->map(function ($items) {
                return Collection::make($items)->pluck('id');
            })->toArray();

            $block['content']->browsers = $browsers;
        }

        return $block;
    }
}
