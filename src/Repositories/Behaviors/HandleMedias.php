<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Media;

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
            $newMedia = Media::withTrashed()->find(is_array($media['id']) ? array_first($media['id']) : $media['id']);
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
                if (in_array($role, array_keys($this->model->mediasParams ?? []))
                    || in_array($role, array_keys(config('twill.block_editor.crops')))) {
                    collect($mediasForRole)->each(function ($media) use (&$medias, $role) {
                        $customMetadatas = $media['metadatas']['custom'] ?? [];
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
                                    'metadatas' => json_encode($customMetadatas),
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
                                    'metadatas' => json_encode($customMetadatas),
                                ]);
                            }
                        }
                    });
                }
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

                    $metadatas = json_decode($item->pivot->metadatas, true);
                    $caption = $metadatas['caption'] ?? '';
                    $altText = $metadatas['altText'] ?? '';
                    $video = $metadatas['video'] ?? '';

                    $itemForForm = $item->toCmsArray() + [
                        'metadatas' => [
                            'default' => [
                                'caption' => $item->caption,
                                'altText' => $item->alt_text,
                                'video' => '',
                            ],
                            'custom' => [
                                'caption' => $caption, // TODO: add caption and alttext to mediables table
                                'altText' => $altText,
                                'video' => $video,
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
