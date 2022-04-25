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
    use HandleMedias;

    use HandleFiles;

    public function __construct(protected Config $config)
    {
        $blockModel = twillModel('block');
        $this->model = new $blockModel;
    }

    /**
     * @return mixed[]
     */
    public function getCrops(string $role): array
    {
        return $this->config->get('twill.block_editor.crops')[$role];
    }

    public function hydrate($object, $fields): \A17\Twill\Models\Model
    {
        if (Schema::hasTable(config('twill.related_table', 'twill_related'))) {
            $relatedItems = Collection::make();

            Collection::make($fields['browsers'])->each(function ($items, $browserName) use (&$relatedItems): void {
                Collection::make($items)->each(function ($item) use ($browserName, &$relatedItems): void {
                    try {
                        $repository = $this->getModelRepository($item['endpointType'] ?? $browserName);
                        $relatedItems->push((object) [
                            'related' => $repository->getById($item['id']),
                            'browser_name' => $browserName,
                        ]);

                    } catch (ReflectionException $reflectionException) {
                        Log::error($reflectionException);
                    }
                });
            });

            $object->setRelation('relatedItems', $relatedItems);
        }

        return parent::hydrate($object, $fields);
    }

    /**
     * @param HasMedias|HasFiles $object
     */
    public function afterSave($object, $fields): void
    {
        if (Schema::hasTable(config('twill.related_table', 'twill_related')) && isset($fields['browsers'])) {
            Collection::make($fields['browsers'])->each(function ($items, $browserName) use ($object): void {
                $object->saveRelated($items, $browserName);
            });
        }

        parent::afterSave($object, $fields);
    }

    public function afterDelete($object): void
    {
        $object->medias()->sync([]);
        $object->files()->sync([]);

        if (Schema::hasTable(config('twill.related_table', 'twill_related'))) {
            $object->relatedItems()->delete();
        }
    }

    /**
     * @param mixed[] $block
     * @return mixed[]
     */
    public function buildFromCmsArray(array $block, bool $repeater = false): array
    {
        $blockInstance = BlockConfig::getForComponent($block['type'], $repeater);

        $block['type'] = $blockInstance->name;

        $block['instance'] = $blockInstance;

        $block['content'] = empty($block['content']) ? new \stdClass : (object) $block['content'];

        if ($block['browsers']) {
            $browsers = Collection::make($block['browsers'])->map(function ($items): \Illuminate\Support\Collection {
                return Collection::make($items)->pluck('id');
            })->toArray();

            $block['content']->browsers = $browsers;
        }

        return $block;
    }
}
