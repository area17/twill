<?php

namespace App\JsonApi\V1\Blocks;

use A17\Twill\API\JsonApi\V1\Blocks\BlockContent;

class BlockContentText extends BlockContent
{
    public function content(): iterable
    {
        return [
            'html' => $this->resource->input('html'),
        ];
    }
}
