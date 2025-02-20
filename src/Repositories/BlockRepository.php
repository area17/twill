<?php

namespace A17\Twill\Repositories;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Models\Block;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\RelatedItem;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Services\Blocks\Block as BlockConfig;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Collection;

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
        $relatedItems = collect($fields['browsers'])
            ->flatMap(fn($items, $browserName) => collect($items)
                ->map(fn($item, $position) => new RelatedItem([
                    'subject_id' => $model->getKey(),
                    'subject_type' => $model->getMorphClass(),
                    'related_id' => $item['id'],
                    'related_type' => $item['endpointType'],
                    'browser_name' => $browserName,
                    'position' => $position,
                ])));

        $model->setRelation('relatedItems', $relatedItems);
        $model->loadMissing('relatedItems.related');

        return parent::hydrate($model, $fields);
    }

    /** @param Block $model */
    public function afterSave(TwillModelContract $model, array $fields): void
    {
        if (!empty($fields['browsers'])) {
            $browserNames = collect($fields['browsers'])->each(function ($items, $browserName) use ($model) {
                // This will create items or delete them if they are missing
                $model->saveRelated($items, $browserName);
            })->keys();

            // Delete all the related items that were emptied
            RelatedItem::query()->whereMorphedTo('subject', $model)->whereNotIn('browser_name', $browserNames)->delete();
        } else {
            $model->clearAllRelated();
        }

        parent::afterSave($model, $fields);
    }

    /** @param Block $object */
    public function afterDelete(TwillModelContract $object): void
    {
        $object->medias()->detach();
        $object->files()->detach();

        $object->clearAllRelated();
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
