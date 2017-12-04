<?php

namespace A17\CmsToolkit\Repositories\Behaviors;

use A17\CmsToolkit\Models\Media;
use ImageService;

trait HandleMedias
{
    public function hydrateHandleMedias($object, $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('medias')) {
            return $object;
        }

        $mediasCollection = collect();
        $mediasFromFields = $this->getMedias($fields);

        $mediasFromFields->each(function ($media) use ($object, $mediasCollection) {
            $newMedia = Media::withTrashed()->find($media['id']);
            $pivot = $newMedia->newPivot($object, array_except($media, ['id']), 'mediables', true);
            $newMedia->setRelation('pivot', $pivot);
            $mediasCollection->push($newMedia);
        });

        $object->setRelation('medias', $mediasCollection);

        return $object;
    }

    public function afterSaveHandleMedias($object, $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('medias')) {
            return;
        }

        $object->medias()->sync([]);

        $this->getMedias($fields)->each(function ($media) use ($object) {
            $object->medias()->attach($media['id'], array_except($media, ['id']));
        });
    }

    private function getMedias($fields)
    {
        $medias = collect();

        if (isset($fields['medias'])) {
            foreach ($fields['medias'] as $role => $mediasForRole) {
                collect($mediasForRole)->each(function ($media) use (&$medias, $role) {
                    if (isset($media['crops'])) {
                        foreach ($media['crops'] as $cropName => $cropData) {
                            $medias->push([
                                'id' => $media['id'],
                                'crop' => $cropName,
                                'role' => $role,
                                'ratio' => $cropData['name'],
                                'crop_w' => $cropData['width'],
                                'crop_h' => $cropData['height'],
                                'crop_x' => $cropData['x'],
                                'crop_y' => $cropData['y'],
                            ]);
                        }
                    } else {
                        foreach ($this->getCrops($role) as $cropName => $cropDefinitions) {
                            $medias->push([
                                'id' => $media['id'],
                                'crop' => $cropName,
                                'role' => $role,
                                'ratio' => array_first($cropDefinitions)['name'],
                                'crop_w' => null,
                                'crop_h' => null,
                                'crop_x' => null,
                                'crop_y' => null,
                            ]);
                        }
                    }
                });
            }
        }

        return $medias;
    }

    public function getFormFieldsHandleMedias($object, $fields)
    {
        $fields['medias'] = null;

        if ($object->has('medias')) {
            foreach ($object->medias->groupBy('pivot.role') as $role => $mediasByRole) {
                foreach ($mediasByRole->groupBy('id') as $id => $mediasById) {
                    $item = $mediasById->first();

                    $itemForForm = [
                        'id' => $item->id,
                        'name' => $item->filename,
                        'src' => ImageService::getCmsUrl($item->uuid, ["h" => "256"]),
                        'original' => ImageService::getRawUrl($item->uuid),
                        'width' => $item->width,
                        'height' => $item->height,
                        'metadatas' => [
                            'default' => [
                                'caption' => $item->caption,
                                'altText' => $item->alt_text,
                            ],
                            'custom' => [
                                'caption' => null, // TODO: add caption and alttext to mediables table
                                'altText' => null,
                            ],
                        ],
                    ];

                    foreach ($mediasById->groupBy('pivot.crop') as $crop => $mediaByCrop) {
                        $media = $mediaByCrop->first();
                        $itemForForm['crops'][$crop] = [
                            'name' => $media->pivot->ratio,
                            'width' => $media->pivot->crop_w,
                            'height' => $media->pivot->crop_h,
                            'x' => $media->pivot->crop_x,
                            'y' => $media->pivot->crop_y,
                        ];
                    }

                    $fields['medias'][$role][] = $itemForForm;
                }
            }
        }

        return $fields;
    }

    public function getCrops($role)
    {
        return $this->model->mediasParams[$role];
    }
}
