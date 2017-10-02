<?php

namespace A17\CmsToolkit\Repositories\Behaviors;

use A17\CmsToolkit\Models\Media;

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

    public function getFormFieldsHandleMedias($object, $fields)
    {
        $fields['medias'] = null;

        if ($object->has('medias')) {

            foreach ($object->medias->groupBy('pivot.role') as $role => $mediasByRole) {
                foreach ($mediasByRole->groupBy('id') as $id => $mediasById) {
                    foreach ($mediasById->groupBy('pivot.crop') as $crop => $mediaByCrop) {
                        $fields['medias'][$role]['images'][$id][$crop] = $mediaByCrop->first();
                    }
                }
            }

            foreach ($object->mediasParams as $role => $crops) {
                foreach ($crops as $crop_name => $ratio) {
                    $fields['medias'][$role]['crops'][$crop_name] = $ratio;
                }
            }
        }

        return $fields;
    }

    public function getCrops($role)
    {
        return $this->model->mediasParams[$role];
    }

    private function getMedias($fields)
    {
        $medias = collect();

        if (isset($fields['medias'])) {
            foreach ($fields['medias'] as $role => $crop) {
                foreach ($crop as $cropName => $croppedMedias) {
                    $mediaCollection = collect($croppedMedias);

                    $transposedMediaCollection = array_map(function (...$items) use ($mediaCollection) {
                        return array_combine($mediaCollection->keys()->all(), $items);
                    }, ...$mediaCollection->values());

                    foreach ($transposedMediaCollection as $media) {
                        $id = $media['id'];
                        $background_position = empty($media['background_position']) ? 'top' : $media['background_position'];
                        unset($media['id']);
                        unset($media['backgroup_position']);

                        // fix square crops from jcrop
                        if ($this->getCrops($role)[$cropName] == 1) {
                            $size = min($media['crop_w'], $media['crop_h']);
                            $media['crop_w'] = $media['crop_h'] = $size;
                        }

                        $medias->push(['id' => $id, 'crop' => $cropName, 'role' => $role, 'background_position' => $background_position] + array_map(function ($crop_coord) {
                            return max(0, intval($crop_coord));
                        }, $media));
                    }
                }
            }
        }

        return $medias;
    }
}
