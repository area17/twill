<?php

namespace A17\CmsToolkit\Repositories\Behaviors;

trait HandleMedias
{
    public function afterSaveHandleMedias($object, $fields)
    {
        $object->medias()->sync([]);

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

                        $object->medias()->attach($id, ['crop' => $cropName, 'role' => $role, 'background_position' => $background_position] + array_map(function ($crop_coord) {
                            return max(0, intval($crop_coord));
                        }, $media));
                    }
                }
            }
        }
    }

    public function getFormFieldsHandleMedias($object, $fields)
    {
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
}
