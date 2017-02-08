<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit Block Editor configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to provide the package with your configuration
    | for the Block renderer service.
    | More to come here...
    |
     */
    'blocks_js_path' => '/assets/admin/blocks/blocks.js',
    'blocks_js_rev' => false,

    'blocks' => [
        "blocktitle" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Text",
        "blocktext" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Text",
        "blockquote" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Text",
        "image" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
        "imagegrid" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
        "imagetext" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
        "diaporama" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
        "button" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Link",
        "blockseparator" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Separator",
    ],
    'sitemap_blocks' => [
        'A17\CmsToolkit\Services\BlockEditor\Blocks\Image',
    ],
];
