<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Facades\TwillUtil;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Block;
use A17\Twill\Models\Model;
use A17\Twill\Repositories\BlockRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Log;
use Schema;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

trait HandleBlocks
{
    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @param int $fakeBlockId
     * @param int|null $parentId
     * @param \Illuminate\Support\Collection|null $blocksFromFields
     * @param \Illuminate\Support\Collection|null $mainCollection
     * @return \A17\Twill\Models\Model|null
     */
    public function hydrateHandleBlocks(
        $object,
        $fields,
        &$fakeBlockId = 0,
        $parentId = null,
        $blocksFromFields = null,
        $mainCollection = null
    ) {
        if ($this->shouldIgnoreFieldBeforeSave('blocks')) {
            return null;
        }

        $firstItem = false;
        if ($mainCollection === null) {
            $firstItem = true;
            $mainCollection = Collection::make();
        }
        if ($blocksFromFields === null) {
            $blocksFromFields = $this->getBlocks($object, $fields);
        }

        $blockRepository = app(BlockRepository::class);
        $blocksCollection = $this->getChildrenBlocks(
            $blocksFromFields,
            $blockRepository,
            $parentId,
            $fakeBlockId,
            $mainCollection
        );
        $object->setRelation('blocks', $firstItem ? $mainCollection : $blocksCollection);

        return $object;
    }

    protected function getChildrenBlocks($blocks, $blockRepository, $parentId, &$fakeBlockId, $mainCollection)
    {
        $childBlocksCollection = Collection::make();

        foreach ($blocks as $childBlock) {
            if ($parentId) {
                $childBlock['parent_id'] = $parentId;
            }
            $newChildBlock = $blockRepository->createForPreview($childBlock);

            $fakeBlockId++;
            $newChildBlock->id = $fakeBlockId;
            if (! empty($childBlock['blocks'])) {
                $childBlockHydrated = $this->hydrateHandleBlocks(
                    $newChildBlock,
                    $childBlock,
                    $fakeBlockId,
                    $newChildBlock->id,
                    $childBlock['blocks'],
                    $mainCollection
                );
                $newChildBlock->setRelation('children', $childBlockHydrated->blocks);
            }

            $mainCollection->push($newChildBlock);
            $childBlocksCollection->push($newChildBlock);
        }

        return $childBlocksCollection;
    }

    public function afterSaveHandleBlocks(Model $object, array $fields): void
    {
        if ($this->shouldIgnoreFieldBeforeSave('blocks')) {
            return;
        }

        $blockRepository = app(BlockRepository::class);

        $validator = Validator::make([], []);

        foreach ($fields['blocks'] ?? [] as $block) {
            $blockCmsData = app(BlockRepository::class)->buildFromCmsArray($block);

            /** @var \A17\Twill\Services\Blocks\Block $blockInstance */
            $blockInstance = $blockCmsData['instance'];

            // Figure out if the class has translations.
            $handleTranslations = property_exists($object, 'translatedAttributes');

            try {
                $this->validate(
                    $block['content'],
                    $block['id'],
                    $blockInstance->getRules(),
                    $handleTranslations ? $blockInstance->getRulesForTranslatedFields() : []
                );
            } catch (ValidationException $e) {
                $validator->errors()->merge($e->errors());
            }
        }

        if ($validator->errors()->isNotEmpty()) {
            throw new ValidationException($validator);
        }

        $existingBlockIds = $object->blocks()->pluck('id')->toArray();

        $usedBlockIds = [];

        foreach ($this->getBlocks($object, $fields) as $blockData) {
            $this->updateOrCreateBlock($blockRepository, $blockData, $existingBlockIds, $usedBlockIds);
        }

        // Delete the unused existing blocks.
        $unusedBlockIds = array_diff($existingBlockIds, $usedBlockIds);
        $blockRepository->bulkDelete($unusedBlockIds);
    }

    private function updateOrCreateBlock(
        BlockRepository $blockRepository,
        array $blockData,
        array $existingBlockIds,
        array &$usedBlockIds
    ): void {
        // Find an existing block id based on the frontend id.
        if (
            ! in_array($blockData['id'] ?? null, $existingBlockIds, false) &&
            $id = TwillUtil::hasBlockIdFor($blockData['id'])
        ) {
            $originalBlockId = $blockData['id'];
            $blockData['id'] = $id;
        }
        // Check if the block already exists.
        if (in_array($blockData['id'] ?? null, $existingBlockIds, false)) {
            $blockObject = $this->updateBlock($blockRepository, $blockData, $existingBlockIds, $usedBlockIds);
        } else {
            $blockObject = $this->createBlock($blockRepository, $blockData, $existingBlockIds, $usedBlockIds);
            TwillUtil::registerBlockId($originalBlockId ?? $blockData['id'], $blockObject->id);
        }

        $usedBlockIds[] = $blockObject->id;
    }

    private function updateBlock(
        BlockRepository $blockRepository,
        array $blockData,
        array $existingBlockIds,
        array &$usedBlockIds
    ): Block {
        $blockRepository->update($blockData['id'], $blockData);
        $blockCreated = $blockRepository->findOrFail($blockData['id']);

        $this->updateOrCreateChildBlocks(
            $blockCreated,
            $blockRepository,
            $blockData,
            $existingBlockIds,
            $usedBlockIds
        );

        return $blockCreated;
    }

    private function validate(array $formData, int $id, array $basicRules, array $translatedFieldRules): void
    {
        $finalValidator = Validator::make([], []);
        foreach ($translatedFieldRules as $field => $rules) {
            foreach (config('translatable.locales') as $locale) {
                $data = $formData[$field][$locale] ?? null;
                $validator = Validator::make([$field => $data], [$field => $rules]);
                foreach ($validator->messages()->getMessages() as $key => $errors) {
                    foreach ($errors as $error) {
                        $finalValidator->getMessageBag()->add("blocks.$id" . "[$key][$locale]", $error);
                        $finalValidator->getMessageBag()->add("blocks.$locale", 'Failed');
                    }
                }
            }
        }
        foreach ($basicRules as $field => $rules) {
            $validator = Validator::make([$field => $formData[$field] ?? null], [$field => $rules]);
            foreach ($validator->messages()->getMessages() as $key => $errors) {
                foreach ($errors as $error) {
                    $finalValidator->getMessageBag()->add("blocks[$id][$key]", $error);
                }
            }
        }

        if ($finalValidator->errors()->isNotEmpty()) {
            throw new ValidationException($finalValidator);
        }
    }

    /**
     * Create a block from formFields, and recursively create it's child blocks.
     */
    private function createBlock(
        BlockRepository $blockRepository,
        array $blockData,
        array $existingBlockIds,
        array &$usedBlockIds
    ): Block {
        $blockCreated = $blockRepository->create($blockData);

        $this->updateOrCreateChildBlocks(
            $blockCreated,
            $blockRepository,
            $blockData,
            $existingBlockIds,
            $usedBlockIds
        );

        return $blockCreated;
    }

    private function updateOrCreateChildBlocks(
        Block $parentBlock,
        BlockRepository $blockRepository,
        array $blockData,
        array $existingBlockIds,
        array &$usedBlockIds
    ): void {
        foreach ($blockData['blocks'] as $childBlock) {
            $childBlock['parent_id'] = $parentBlock->id;

            $this->updateOrCreateBlock($blockRepository, $childBlock, $existingBlockIds, $usedBlockIds);
        }
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
                $block['blocks'] = $this->getChildBlocks($object, $block);

                $blocks->push($block);
            }
        }

        return $blocks;
    }

    /**
     * Recursively generate child blocks from the fields of a block.
     *
     * @param \A17\Twill\Models\Model $object
     * @param array $parentBlockFields
     * @return \Illuminate\Support\Collection
     */
    private function getChildBlocks($object, $parentBlockFields)
    {
        $childBlocksList = Collection::make();

        foreach ($parentBlockFields['blocks'] as $childKey => $childBlocks) {
            foreach ($childBlocks as $index => $childBlock) {
                $childBlock = $this->buildBlock($childBlock, $object, true);
                $childBlock['child_key'] = $childKey;
                $childBlock['position'] = $index + 1;
                $childBlock['editor_name'] = $parentBlockFields['editor_name'] ?? 'default';
                $childBlock['blocks'] = $this->getChildBlocks($object, $childBlock);

                $childBlocksList->push($childBlock);
            }
        }

        return $childBlocksList;
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
            foreach ($object->blocks as $block) {
                $blockTypeConfig = TwillBlocks::findByName($block->type);

                if (is_null($blockTypeConfig)) {
                    continue;
                }

                $blockItem = [
                    'id' => $block->id,
                    'type' => $blockTypeConfig->component,
                    'title' => $blockTypeConfig->title,
                    'name' => $block->editor_name ?? 'default',
                    'titleField' => $blockTypeConfig->titleField,
                    'hideTitlePrefix' => $blockTypeConfig->hideTitlePrefix,
                    // @todo: Figure out what attributes were coming from/used for.
                    // $blockTypeConfig['attributes'] ?? []
                    'attributes' => [],
                ];

                if (isset($block->parent_id)) {
                    $fields['blocksRepeaters']["blocks-{$block->parent_id}_{$block->child_key}"][] = $blockItem + [
                            'trigger' => $blockTypeConfig->trigger,
                            'max' => $blockTypeConfig->max,
                        ];
                } else {
                    $fields['blocks'][$blockItem['name']][] = $blockItem + [
                            'icon' => $blockTypeConfig->icon,
                        ];
                }

                $fields['blocksFields'][] = Collection::make($block['content'])->filter(function ($value, $key) {
                    return $key !== 'browsers';
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
                        $fields['blocksMedias'][] = Collection::make($medias)->mapWithKeys(
                            function ($mediasByLocale, $locale) use ($block) {
                                return Collection::make($mediasByLocale)->mapWithKeys(
                                    function ($value, $key) use ($block, $locale) {
                                        return [
                                            "blocks[$block->id][$key][$locale]" => $value,
                                        ];
                                    }
                                );
                            }
                        )->filter()->toArray();
                    } else {
                        $fields['blocksMedias'][] = Collection::make($medias)->mapWithKeys(
                            function ($value, $key) use ($block) {
                                return [
                                    "blocks[$block->id][$key]" => $value,
                                ];
                            }
                        )->filter()->toArray();
                    }
                }

                $files = $blockFormFields['files'];

                if ($files) {
                    Collection::make($files)->each(function ($rolesWithFiles, $locale) use (&$fields, $block) {
                        $fields['blocksFiles'][] = Collection::make($rolesWithFiles)->mapWithKeys(
                            function ($files, $role) use ($locale, $block) {
                                return [
                                    "blocks[$block->id][$role][$locale]" => $files,
                                ];
                            }
                        )->toArray();
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
            if (Schema::hasTable(config('twill.related_table', 'twill_related')) && $block->getRelated($relation)
                    ->isNotEmpty()) {
                $items = $this->getFormFieldsForRelatedBrowser($block, $relation);
                foreach ($items as &$item) {
                    if (! isset($item['edit'])) {
                        try {
                            $item['edit'] = moduleRoute(
                                $relation,
                                config('twill.block_editor.browser_route_prefixes.' . $relation),
                                'edit',
                                $item['id']
                            );
                        } catch (RouteNotFoundException $e) {
                            report($e);
                            Log::notice(
                                "Twill warning: The url for the \"{$relation}\" browser items can't " .
                                "be resolved. You might be missing a {$relation} key in your " .
                                'twill.block_editor.browser_route_prefixes configuration.'
                            );
                        }
                    }
                }
            } else {
                try {
                    $relationRepository = $this->getModelRepository($relation);
                    $relatedItems = $relationRepository->get([], ['id' => $ids], [], -1);
                } catch (\Throwable $th) {
                    $relatedItems = collect();
                }
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
                            'edit' => moduleRoute(
                                $relation,
                                config('twill.block_editor.browser_route_prefixes.' . $relation),
                                'edit',
                                $relatedElement->id
                            ),
                        ] + (classHasTrait($relatedElement, HasMedias::class) ? [
                            'thumbnail' => $relatedElement->defaultCmsImage(['w' => 100, 'h' => 100]),
                        ] : []);
                })->toArray();
            }

            return [
                "blocks[$block->id][$relation]" => $items,
            ];
        })->filter()->toArray();
    }
}
