<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use A17\CmsToolkit\Services\BlockEditor\RenderBlocks;
use Illuminate\Http\Request;

class BlocksController extends Controller
{
    public function preview(RenderBlocks $renderBlocks)
    {
        return $renderBlocks->fromSingleJsonToHtml(json_encode(request()->all()));
    }
}
