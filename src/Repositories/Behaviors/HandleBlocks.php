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
            $block['content'] = json_encode($block['content']);
            $blockRepository->create($block);
        });
    }

    private function getBlocks($fields)
    {
        $blocks = collect();

        if (isset($fields['blocks'])) {
            foreach ($fields['blocks'] as $index => $block) {
                $block['position'] = $index + 1;
                $blocks->push($block);
            }
        }

        return $blocks;
    }

    public function getFormFieldsHandleBlocks($object, $fields)
    {
        $fields['blocks'] = null;

        if ($object->has('blocks')) {
            foreach ($object->blocks as $block) {
                $fields['blocks'][] = $block;
            }
        }

        return $fields;
    }
}
