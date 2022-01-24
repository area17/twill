<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Helpers\TwillBlock;
use A17\Twill\Repositories\BlockRepository;
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\Factory as ViewFactory;

class BlocksController extends Controller
{

    /**
     * Render an HTML preview of a single block.
     * This is used by the full screen content editor.
     *
     * @param BlockRepository $blockRepository
     * @param Application $app
     * @param ViewFactory $viewFactory
     * @param Request $request
     * @return string
     */
    public function preview(
        BlockRepository $blockRepository,
        Application $app,
        ViewFactory $viewFactory,
        Request $request,
        Config $config
    ) {
        $blocksCollection = Collection::make();

        if ($request->has('activeLanguage')) {
            $app->setLocale($request->get('activeLanguage'));
        }

        $block = $blockRepository->buildFromCmsArray($request->except('activeLanguage'));

        $childBlocksList = $this->getChildrenBlock($block, $blockRepository);
        $block['blocks'] = $childBlocksList;
        $block['children'] = $childBlocksList;

        $newBlock = $blockRepository->createForPreview($block);

        $blockId = 1;
        $newBlock->id = $blockId;

        $blocksCollection->push($newBlock);

        $this->getChildrenPreview($block['blocks'], $blocksCollection, $newBlock->id, $blockId, $blockRepository);

        $renderedBlocks = $blocksCollection->where('parent_id', null)
            ->map(function ($blockToRender) use ($block, $blocksCollection, $viewFactory, $config) {
                try {
                    if ($config->get('twill.block_editor.block_preview_render_childs') ?? true) {
                        $childBlocks = $blocksCollection->where('parent_id', $blockToRender->id);
                        $renderedChildViews = $childBlocks->map(function ($childBlock) use ($viewFactory, $config) {
                            $view = $this->getBlockView($childBlock->type, $config);

                            return $viewFactory->exists($view) ? $viewFactory->make($view, [
                                'block' => $childBlock,
                            ])->render() : $viewFactory->make('twill::errors.block', [
                                'view' => $view,
                            ])->render();
                        })->implode('');
                    }

                    $blockToRender->childs = $blocksCollection->where('parent_id', $blockToRender->id);
                    $blockToRender->children = $blockToRender->childs;

                    $data = [
                        'block' => $blockToRender,
                    ];

                    $view = $this->getBlockView($blockToRender->type, $config);

                    $data = $block['instance']->getData($data, $blockToRender);

                    $error = '';

                    if ($viewFactory->exists($view)) {
                        return $viewFactory->make($view, $data)->render() . ($renderedChildViews ?? '');
                    }
                } catch (\Exception $e) {
                    $error = $e->getMessage() . ' in ' . $e->getFile();
                }

                return $viewFactory->make('twill::errors.block', ['view' => $view, 'error' => $error])->render();
            })->implode('');

        $view = $viewFactory->exists($config->get('twill.block_editor.block_single_layout'))
            ? $viewFactory->make($config->get('twill.block_editor.block_single_layout'), [
                'block' => $block,
            ])
            : $viewFactory->make('twill::errors.block_layout', [
                'view' => $config->get('twill.block_editor.block_single_layout'),
            ]);

        $viewFactory->inject('content', $renderedBlocks);

        return html_entity_decode($view);
    }

    protected function getChildrenBlock($block, $blockRepository)
    {
        $childBlocksList = Collection::make();
        foreach ($block['blocks'] as $childKey => $childBlocks) {
            foreach ($childBlocks as $index => $childBlock) {
                $childBlock = $blockRepository->buildFromCmsArray($childBlock, true);
                $childBlock['child_key'] = $childKey;
                $childBlock['position'] = $index + 1;
                if (!empty($childBlock['blocks'])) {
                    $childBlock['children'] = $this->getChildrenBlock($childBlock, $blockRepository);
                }
                $childBlocksList->push($childBlock);
            }
        }
        return $childBlocksList;
    }

    protected function getChildrenPreview($blocks, $blocksCollection, $parentId, &$blockId, $blockRepository)
    {
        $blocks->each(function ($childBlock) use (&$blockId, $parentId, $blocksCollection, $blockRepository) {
            $childBlock['parent_id'] = $parentId;
            $blockId++;
            $newChildBlock = $blockRepository->createForPreview($childBlock);
            $newChildBlock->id = $blockId;
            if (!empty($childBlock['children'])) {
                $childrenCollection = Collection::make();
                $this->getChildrenPreview(
                    $childBlock['children'],
                    $childrenCollection,
                    $newChildBlock->id,
                    $blockId,
                    $blockRepository
                );
                $newChildBlock->setRelation('children', $childrenCollection);
            }
            $blocksCollection->push($newChildBlock);
        });
    }

    /**
     * Determines a view name for a given block type.
     *
     * @param string $blockType
     * @param Config $config
     * @return string
     */
    private function getBlockView($blockType, $config)
    {
        $view = $config->get('twill.block_editor.block_views_path') . '.' . $blockType;

        $customViews = $config->get('twill.block_editor.block_views_mappings');

        if (array_key_exists($blockType, $customViews)) {
            $view = $customViews[$blockType];
        }

        return $view;
    }
}
