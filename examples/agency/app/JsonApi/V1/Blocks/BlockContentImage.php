<?php

namespace App\JsonApi\V1\Blocks;

use A17\Twill\API\JsonApi\V1\Blocks\BlockContent;

class BlockContentImage extends BlockContent
{
    public function content(): iterable
    {
        return [
//            'title' => $this->resource->translatedInput('title'),
        ];
    }
}
