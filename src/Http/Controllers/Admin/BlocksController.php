<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Repositories\BlockRepository;

class BlocksController extends Controller
{
    public function preview(BlockRepository $blockRepository)
    {
        $blocksCollection = collect();
        $childBlocksList = collect();

        if (request()->has('activeLanguage')) {
            app()->setLocale(request('activeLanguage'));
        }

        $block = $blockRepository->buildFromCmsArray(request()->except('activeLanguage'));

        foreach ($block['blocks'] as $childKey => $childBlocks) {
            foreach ($childBlocks as $index => $childBlock) {
                $childBlock = $blockRepository->buildFromCmsArray($childBlock, true);
                $childBlock['child_key'] = $childKey;
                $childBlock['position'] = $index + 1;

                $childBlocksList->push($childBlock);
            }
        }

        $block['blocks'] = $childBlocksList;

        $newBlock = $blockRepository->createForPreview($block);

        $newBlock->id = 1;

        $blocksCollection->push($newBlock);

        $block['blocks']->each(function ($childBlock) use ($newBlock, $blocksCollection, $blockRepository) {
            $childBlock['parent_id'] = $newBlock->id;
            $newChildBlock = $blockRepository->createForPreview($childBlock);
            $blocksCollection->push($newChildBlock);
        });

        $renderedBlocks = $blocksCollection->where('parent_id', null)->map(function ($block) use ($blocksCollection) {
            if (config('twill.block_editor.block_preview_render_childs') ?? true) {
                $childBlocks = $blocksCollection->where('parent_id', $block->id);
                $renderedChildViews = $childBlocks->map(function ($childBlock) {
                    $view = $this->getBlockView($childBlock->type);

                    return view()->exists($view) ? view($view, [
                        'block' => $childBlock,
                    ])->render() : view('twill::errors.block', [
                        'view' => $view,
                    ])->render();
                })->implode('');
            }

            $block->childs = $blocksCollection->where('parent_id', $block->id);
            $block->children = $block->childs;

            $view = $this->getBlockView($block->type);

            return view()->exists($view) ? (view($view, [
                'block' => $block,
            ])->render() . ($renderedChildViews ?? '')) : view('twill::errors.block', [
                'view' => $view,
            ])->render();

        })->implode('');

        $view = view()->exists(config('twill.block_editor.block_single_layout'))
        ? view(config('twill.block_editor.block_single_layout'))
        : view('twill::errors.block_layout', [
            'view' => config('twill.block_editor.block_single_layout'),
        ]);

        $view->getFactory()->inject('content', $renderedBlocks);

        return html_entity_decode($view);
    }

    private function getBlockView($blockType)
    {
        $view = config('twill.block_editor.block_views_path') . '.' . $blockType;

        $customViews = config('twill.block_editor.block_views_mappings');

        if (array_key_exists($blockType, $customViews)) {
            $view = $customViews[$blockType];
        }

        return $view;
    }

}
