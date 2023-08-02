<?php

namespace App\JsonApi\V1\Blocks;

use A17\Twill\API\JsonApi\V1\Blocks\BlockContent;

class BlockContentQuote extends BlockContent
{

    public function content(): iterable
    {
        return [
            'quote' => $this->resource->input('quote'),
        ];
    }
}
