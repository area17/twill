<?php

namespace A17\Twill\Repositories;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Services\Blocks\Block as BlockConfig;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use ReflectionException;

class BlockRepository extends ModuleRepository
{
    use HandleMedias;
    use HandleFiles;

    protected Config $config;

    public function __construct(Config $config)
    {
        $blockModel = twillModel('block');
        $this->model = new $blockModel();
        $this->config = $config;
    }

    public function getCrops(string $role): array
    {
        return TwillBlocks::getAllCropConfigs()[$role];
    }

    public function hydrate(TwillModelContract $model, array $fields): TwillModelContract
    {
        if (Schema::hasTable(config('twill.related_table', 'twill_related'))) {
            $relatedItems = Collection::make();

            Collection::make($fields['browsers'])->each(function ($items, $browserName) use (&$relatedItems) {
                Collection::make($items)->each(function ($item) use ($browserName, &$relatedItems) {
                    try {
                        // @todo: Repository could be null.
                        $repository = $this->getModelRepository($item['endpointType'] ?? $browserName);
                        $relatedItems->push(
                            (object) [
                                'related' => $repository->getById($item['id']),
                                'browser_name' => $browserName,
                            ]
                        );
                    } catch (ReflectionException $reflectionException) {
                        Log::error($reflectionException);
                    }
                });
            });

            $model->setRelation('relatedItems', $relatedItems);
        }

        return parent::hydrate($model, $fields);
    }

    public function afterSave(TwillModelContract $model, array $fields): void
    {
        if (Schema::hasTable(config('twill.related_table', 'twill_related'))) {
            $model->clearAllRelated();

            if (isset($fields['browsers'])) {
                Collection::make($fields['browsers'])->each(function ($items, $browserName) use ($model) {
                    $model->saveRelated($items, $browserName);
                });
            }
        }

        parent::afterSave($model, $fields);
    }

    public function afterDelete(TwillModelContract $object): void
    {
        $object->medias()->sync([]);
        $object->files()->sync([]);

        if (Schema::hasTable(config('twill.related_table', 'twill_related'))) {
            $object->clearAllRelated();
        }
    }

    public function buildFromCmsArray(array $block, bool $repeater = false): array
    {
        $blockInstance = BlockConfig::getForComponent($block['type'], $repeater);

        $block['type'] = $blockInstance->name;

        $block['instance'] = $blockInstance;

        $block['content'] = empty($block['content']) ? new \stdClass() : (object) $block['content'];

        if ($block['browsers'] ?? null) {
            $browsers = Collection::make($block['browsers'])->map(function ($items) {
                return Collection::make($items)->pluck('id');
            })->toArray();

            $block['content']->browsers = $browsers;
        }

        return $block;
    }
}
