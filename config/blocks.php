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
    'blocks' => [
        "blocktext" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Text",
        "blockquote" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Text",
        "blocktitle" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Text",
        "imagefull" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
        "imagesimple" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
        "imagegrid" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
        "imagetext" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
        "diaporama" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Image",
        "blockseparator" => "A17\CmsToolkit\Services\BlockEditor\Blocks\Separator",
    ],
    'sitemap_blocks' => [
        'A17\CmsToolkit\Services\BlockEditor\Blocks\Image',
    ],
];
