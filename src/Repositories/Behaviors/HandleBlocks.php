<?php

namespace A17\CmsToolkit\Repositories\Behaviors;

use A17\CmsToolkit\Repositories\BlockRepository;

trait HandleBlocks
{
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

    private function getBlocks($object, $fields)
    {
        $blocks = collect();

        if (isset($fields['blocks'])) {

            foreach ($fields['blocks'] as $index => $block) {
                $block = $this->buildBlock($block, $object);
                $block['position'] = $index + 1;

                $childBlocksList = collect();

                foreach ($block['blocks'] as $childKey => $childBlocks) {
                    foreach ($childBlocks as $index => $childBlock) {
                        $childBlock = $this->buildBlock($childBlock, $object);

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

    private function buildBlock($block, $object)
    {
        $block['blockable_id'] = $object->id;
        $block['blockable_type'] = $object->getMorphClass();

        $block['type'] = collect(config('cms-toolkit.block_editor.blocks'))->search(function ($configBlock) use ($block) {
            return $configBlock['component'] === $block['type'];
        });

        $block['content'] = empty($block['content']) ? new \stdClass : $block['content'];

        return $block;
    }

    public function getFormFieldsHandleBlocks($object, $fields)
    {
        $fields['blocks'] = null;

        if ($object->has('blocks')) {

            $blocksConfig = config('cms-toolkit.block_editor.blocks');

            foreach ($object->blocks as $block) {
                $blockItem = [
                    'id' => $block->id,
                    'type' => $blocksConfig[$block->type]['component'],
                    'icon' => $blocksConfig[$block->type]['icon'],
                    'title' => $blocksConfig[$block->type]['title'],
                    'attributes' => $blocksConfig[$block->type]['attributes'] ?? [],
                ];

                if ($block->parent_id) {
                    $fields['blocksRepeaters']["blocks-{$block->parent_id}_{$block->child_key}"][] = $blockItem;
                } else {
                    $fields['blocks'][] = $blockItem;
                }

                $fields['blocksFields'][] = collect($block['content'])->map(function ($value, $key) use ($block) {
                    return [
                        'name' => "blocks[$block->id][$key]",
                        'value' => $value,
                    ];
                })->filter()->values()->toArray();

                $medias = app(BlockRepository::class)->getFormFields($block)['medias'];

                if ($medias) {
                    $fields['blocksMedias'][] = collect($medias)->mapWithKeys(function ($value, $key) use ($block) {
                        return [
                            "blocks[$block->id][$key]" => $value,
                        ];
                    })->filter()->toArray();
                }
            }

            if ($fields['blocksFields'] ?? false) {
                $fields['blocksFields'] = call_user_func_array('array_merge', $fields['blocksFields'] ?? []);
            }

            if ($fields['blocksMedias'] ?? false) {
                $fields['blocksMedias'] = call_user_func_array('array_merge', $fields['blocksMedias'] ?? []);
            }
        }

        return $fields;
    }
}
