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

        $this->getBlocks($fields)->each(function ($block) use ($object, $blockRepository) {
            $block['blockable_id'] = $object->id;
            $block['blockable_type'] = $object->getMorphClass();
            $blockRepository->create($block);
        });
    }

    private function getBlocks($fields)
    {
        $blocks = collect();

        if (isset($fields['blocks'])) {
            $blocksConfig = config('cms-toolkit.block-editor.blocks');

            foreach ($fields['blocks'] as $index => $block) {
                $block['position'] = $index + 1;

                $block['type'] = collect($blocksConfig)->search(function ($configBlock) use ($block) {
                    return $configBlock['component'] === $block['type'];
                });

                $block['content'] = empty($block['content']) ? new \stdClass : $block['content'];

                $blocks->push($block);
            }
        }

        return $blocks;
    }

    public function getFormFieldsHandleBlocks($object, $fields)
    {
        $fields['blocks'] = null;

        if ($object->has('blocks')) {

            $blocksConfig = config('cms-toolkit.block-editor.blocks');

            foreach ($object->blocks as $block) {
                $fields['blocks'][] = [
                    'id' => $block->id,
                    'type' => $blocksConfig[$block->type]['component'],
                    'icon' => $blocksConfig[$block->type]['icon'],
                    'title' => $blocksConfig[$block->type]['title'],
                    'attributes' => $blocksConfig[$block->type]['attributes'] ?? [],
                ];

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
