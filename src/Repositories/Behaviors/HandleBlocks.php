<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Repositories\BlockRepository;
use Illuminate\Support\Collection;

trait HandleBlocks
{
    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return \A17\Twill\Models\Model|void
     */
    public function hydrateHandleBlocks($object, $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('blocks')) {
            return;
        }

        $blocksCollection = Collection::make();
        $blocksFromFields = $this->getBlocks($object, $fields);
        $blockRepository = app(BlockRepository::class);

        $fakeBlockId = 1;

        foreach ($blocksFromFields as $block) {
            $newBlock = $blockRepository->createForPreview($block);

            $newBlock->id = $fakeBlockId;
            $fakeBlockId++;

            $childBlocksCollection = Collection::make();

            foreach ($block['blocks'] as $childBlock) {
                $childBlock['parent_id'] = $newBlock->id;

                $newChildBlock = $blockRepository->createForPreview($childBlock);

                $newChildBlock->id = $fakeBlockId;
                $fakeBlockId++;

                $blocksCollection->push($newChildBlock);
                $childBlocksCollection->push($newChildBlock);
            }

            $newBlock->setRelation('children', $childBlocksCollection);

            $blocksCollection->push($newBlock);
        }

        $object->setRelation('blocks', $blocksCollection);

        return $object;
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return void
     */
    public function afterSaveHandleBlocks($object, $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('blocks')) {
            return;
        }

        $blockRepository = app(BlockRepository::class);

        $blockRepository->bulkDelete($object->blocks()->pluck('id')->toArray());

        $this->getBlocks($object, $fields)->each(function ($block) use ($object, $blockRepository) {

            $blockCreated = $blockRepository->create($block);

            $block['blocks']->each(function ($childBlock) use ($blockCreated, $blockRepository) {
                $childBlock['parent_id'] = $blockCreated->id;
                $blockRepository->create($childBlock);
            });
        });
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return \Illuminate\Support\Collection
     */
    private function getBlocks($object, $fields)
    {
        $blocks = Collection::make();
        if (isset($fields['blocks']) && is_array($fields['blocks'])) {

            foreach ($fields['blocks'] as $index => $block) {
                $block = $this->buildBlock($block, $object);
                $block['position'] = $index + 1;

                $childBlocksList = Collection::make();

                foreach ($block['blocks'] as $childKey => $childBlocks) {
                    foreach ($childBlocks as $index => $childBlock) {
                        $childBlock = $this->buildBlock($childBlock, $object, true);

                        $childBlock['child_key'] = $childKey;
                        $childBlock['position'] = $index + 1;

                        $childBlocksList->push($childBlock);
                    }
                }

                $block['blocks'] = $childBlocksList;

                $blocks->push($block);
            }
        }

        return $blocks;
    }

    /**
     * @param array $block
     * @param \A17\Twill\Models\Model $object
     * @param bool $repeater
     * @return array
     */
    private function buildBlock($block, $object, $repeater = false)
    {
        $block['blockable_id'] = $object->id;
        $block['blockable_type'] = $object->getMorphClass();

        return app(BlockRepository::class)->buildFromCmsArray($block, $repeater);
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleBlocks($object, $fields)
    {
        $fields['blocks'] = null;

        if ($object->has('blocks')) {

            $blocksConfig = config('twill.block_editor');

            foreach ($object->blocks as $block) {

                $isInRepeater = isset($block->parent_id);
                $configKey = $isInRepeater ? 'repeaters' : 'blocks';
                $blockTypeConfig = $blocksConfig[$configKey][$block->type] ?? null;

                if (is_null($blockTypeConfig)) {
                    continue;
                }

                $blockItem = [
                    'id' => $block->id,
                    'type' => $blockTypeConfig['component'],
                    'title' => $blockTypeConfig['title'],
                    'attributes' => $blockTypeConfig['attributes'] ?? [],
                ];

                if ($isInRepeater) {
                    $fields['blocksRepeaters']["blocks-{$block->parent_id}_{$block->child_key}"][] = $blockItem + [
                        'trigger' => $blockTypeConfig['trigger'],
                    ] + (isset($blockTypeConfig['max']) ? [
                        'max' => $blockTypeConfig['max'],
                    ] : []);
                } else {
                    $fields['blocks'][] = $blockItem + [
                        'icon' => $blockTypeConfig['icon'],
                    ];
                }

                $fields['blocksFields'][] = Collection::make($block['content'])->filter(function ($value, $key) {
                    return $key !== "browsers";
                })->map(function ($value, $key) use ($block) {
                    return [
                        'name' => "blocks[$block->id][$key]",
                        'value' => $value,
                    ];
                })->filter()->values()->toArray();

                $blockFormFields = app(BlockRepository::class)->getFormFields($block);

                $medias = $blockFormFields['medias'];

                if ($medias) {
                    if (config('twill.media_library.translated_form_fields', false)) {
                        $fields['blocksMedias'][] = Collection::make($medias)->mapWithKeys(function ($mediasByLocale, $locale) use ($block) {
                            return Collection::make($mediasByLocale)->mapWithKeys(function ($value, $key) use ($block, $locale) {
                                return [
                                    "blocks[$block->id][$key][$locale]" => $value,
                                ];
                            });
                        })->filter()->toArray();
                    } else {
                        $fields['blocksMedias'][] = Collection::make($medias)->mapWithKeys(function ($value, $key) use ($block) {
                            return [
                                "blocks[$block->id][$key]" => $value,
                            ];
                        })->filter()->toArray();
                    }
                }

                $files = $blockFormFields['files'];

                if ($files) {
                    Collection::make($files)->each(function ($rolesWithFiles, $locale) use (&$fields, $block) {
                        $fields['blocksFiles'][] = Collection::make($rolesWithFiles)->mapWithKeys(function ($files, $role) use ($locale, $block) {
                            return [
                                "blocks[$block->id][$role][$locale]" => $files,
                            ];
                        })->toArray();
                    });
                }

                if (isset($block['content']['browsers'])) {
                    $fields['blocksBrowsers'][] = $this->getBlockBrowsers($block);
                }
            }

            if ($fields['blocksFields'] ?? false) {
                $fields['blocksFields'] = call_user_func_array('array_merge', $fields['blocksFields'] ?? []);
            }

            if ($fields['blocksMedias'] ?? false) {
                $fields['blocksMedias'] = call_user_func_array('array_merge', $fields['blocksMedias'] ?? []);
            }

            if ($fields['blocksFiles'] ?? false) {
                $fields['blocksFiles'] = call_user_func_array('array_merge', $fields['blocksFiles'] ?? []);
            }

            if ($fields['blocksBrowsers'] ?? false) {
                $fields['blocksBrowsers'] = call_user_func_array('array_merge', $fields['blocksBrowsers'] ?? []);
            }
        }

        return $fields;
    }

    /**
     * @param \A17\Twill\Models\Block $block
     * @return array
     */
    protected function getBlockBrowsers($block)
    {
        return Collection::make($block['content']['browsers'])->mapWithKeys(function ($ids, $relation) use ($block) {
            $relationRepository = $this->getModelRepository($relation);
            $relatedItems = $relationRepository->get([], ['id' => $ids], [], -1);
            $sortedRelatedItems = array_flip($ids);

            foreach ($relatedItems as $item) {
                $sortedRelatedItems[$item->id] = $item;
            }

            $items = Collection::make(array_values($sortedRelatedItems))->filter(function ($value) {
                return is_object($value);
            })->map(function ($relatedElement) use ($relation) {
                return [
                    'id' => $relatedElement->id,
                    'name' => $relatedElement->titleInBrowser ?? $relatedElement->title,
                    'edit' => moduleRoute($relation, config('twill.block_editor.browser_route_prefixes.' . $relation), 'edit', $relatedElement->id),
                ] + (classHasTrait($relatedElement, HasMedias::class) ? [
                    'thumbnail' => $relatedElement->defaultCmsImage(['w' => 100, 'h' => 100]),
                ] : []);
            })->toArray();

            return [
                "blocks[$block->id][$relation]" => $items,
            ];
        })->filter()->toArray();
    }
}
