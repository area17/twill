<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use A17\CmsToolkit\Services\BlockEditor\RenderBlocks;

class BlocksController extends Controller
{
    public function preview(RenderBlocks $renderBlocks)
    {
        if (config('cms-toolkit.block_editor.use_iframes')) {
            $view = view(config('cms-toolkit.block_editor.iframe_wrapper_view'));
            $view->getFactory()->inject('content', $renderBlocks->fromSingleJsonToHtml(json_encode(request()->all())));
            return view('cms-toolkit::layouts.block_frame', [
                'blockMarkup' => $view->render(),
            ]);
        }

        return $renderBlocks->fromSingleJsonToHtml(json_encode(request()->all()));
    }
}
