<?php

namespace App\Twill\Block;

use A17\Twill\Image\Facades\TwillImage;
use A17\Twill\Services\Blocks\Block;

class ImageBlock extends Block
{
    public function getData(array $data, \A17\Twill\Models\Block $block): array
    {
        $data = parent::getData($data, $block);

        foreach ($block->imageObjects('image', 'desktop') as $imageData) {
            $data['images'][] = [
                'image' => TwillImage::make($block, 'image', $imageData)->crop('desktop'),
                'alt' => $imageData['alt'],
                'video' => $imageData['video'],
                'caption' => $imageData['caption']
            ];
        }

        return $data;
    }
}
