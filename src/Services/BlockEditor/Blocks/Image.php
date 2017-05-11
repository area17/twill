<?php

namespace A17\CmsToolkit\Services\BlockEditor\Blocks;

use A17\CmsToolkit\Repositories\MediaRepository;
use ImageService;

class Image extends BaseBlock
{
    protected $types = [
        'image',
        'imagegrid',
        'imagetext',
        'diaporama',
    ];

    public function imageToHtml()
    {
        return view($this->view('image'), $this->getBlockData());
    }

    public function imagegridToHtml()
    {
        return view($this->view('imagegrid'), $this->getBlockData('grid'));
    }

    public function imagetextToHtml()
    {
        return view($this->view('imagetext'), [
            'title' => $this->getInput('title'),
            'text' => $this->getInput('text'),
            'image' => $this->getImage($this->data['image_id']),
            'crop_params' => $this->getCrop($this->data['image_id_crop']),
            'image_first' => $this->data['image_position'],
        ]);
    }

    public function diaporamaToHtml()
    {
        return view($this->view('diaporama'), [
            'title' => $this->getInput('title'),
        ] + $this->getBlockData() + $this->options);
    }

    public function diaporamaMetadatas()
    {
        return ['image_count' => count($this->getBlockData()['images'])];
    }

    protected function getBlockData($type = 'full', $max_ratio = null)
    {
        if ($type == 'full') {
            return $this->getImagesParams('images', 'crop_params', 'image_id', 'image_id_crop');
        }

        if ($type == 'grid') {
            $paramsLeft = $this->getImagesParams('images_left', 'crop_params_left', 'image_left_id', 'image_left_id_crop');
            $paramsRight = $this->getImagesParams('images_right', 'crop_params_right', 'image_right_id', 'image_right_id_crop');

            return array_merge($paramsLeft, $paramsRight);
        }

        return [];
    }

    protected function getImagesParams($params_images_key, $params_crop_key, $data_image_id_key, $data_image_crop_key)
    {
        $params = [];

        $params[$params_images_key] = !empty($this->data[$data_image_id_key]) ? $this->getMedias($this->data[$data_image_id_key]) : null;

        if (array_first($params[$params_images_key])) {
            $params[$params_crop_key] = $this->getFocalPointCropParams($this->data[$data_image_crop_key], array_first($params[$params_images_key]));
        } else {
            $params[$params_crop_key] = [];
        }

        return $params;
    }

    protected function getMedias($ids_list)
    {
        $ids_array = explode(',', $ids_list);

        $medias_array = array_replace(
            array_flip($ids_array),
            app(MediaRepository::class)->get([], ['id' => $ids_array], [], -1)->getDictionary()
        );

        return array_filter($medias_array, function ($media) {
            return is_object($media);
        });
    }

    public function listImagesForSitemap()
    {
        $images = collect();

        if (isset($this->data['image_id'])) {
            $images = $images->merge($this->getSitemapImagesFrom($this->getMedias($this->data['image_id'])));
        }

        if (isset($this->data['image_left_id'])) {
            $images = $images->merge($this->getSitemapImagesFrom($this->getMedias($this->data['image_left_id'])));
        }

        if (isset($this->data['image_right_id'])) {
            $images = $images->merge($this->getSitemapImagesFrom($this->getMedias($this->data['image_right_id'])));
        }

        return $images->toArray();
    }

    private function getSitemapImagesFrom($medias)
    {
        $images = [];
        foreach ($medias as $media) {
            $images[] = [
                'url' => ImageService::getUrl($media->uuid, ['w' => '900', 'fit' => 'max']),
                'title' => $media->alt_text,
                'caption' => $media->caption,
            ];
        }

        return $images;
    }
}
